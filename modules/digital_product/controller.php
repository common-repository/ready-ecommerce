<?php
    class digital_productController extends controller {
		protected $_mimeTypeFile = 'mime_types.txt';
		public function addUploadMimes($mimes) {
			$ourMimes = $this->getMimeTypes();
			if(!empty($ourMimes)) {
				$mimes = array_merge($mimes, $ourMimes);
			}
			return $mimes;
		}
		public function getMimeTypes() {
			$allMimeTypes = array_map('trim', file($this->getModule()->getModDir(). $this->_mimeTypeFile));
			$availableMimeTypes = array();
			if(!empty($allMimeTypes)) {
				foreach($allMimeTypes as $t) {
					// Let's ignore comented lines
					if(strpos($t, '#') === false) {
						$appTypeMime = array_map('trim', preg_split('/\s/', $t));
						if(!empty($appTypeMime) && is_array($appTypeMime) && ($mimeCount = count($appTypeMime)) > 1) {
							$newMimes = array();
							for($i = 1; $i < $mimeCount; $i++) {
								if(!empty($appTypeMime[$i]))
									$newMimes[] = $appTypeMime[$i];
							}
							if(!empty($newMimes)) {
								$availableMimeTypes[ implode('|', $newMimes) ] = $appTypeMime[0];
							}
						}
					}
				}
			}
			return $availableMimeTypes;
		}
        // uploading file
        public function digitalUpload($data , $postarr) {
            global $post; 
			if(!is_object($post)) return $data;
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $data;
            if(!current_user_can('edit_post', $post->ID)) return $data;
            $post_data = $postarr;
            $params = frame::_()->getModule('digital_product')->getParamsObject();
            $key = 0;
            if (!empty($_FILES)) {
                $files = $_FILES;
                add_filter('upload_dir', array($this, 'changeUploadDir'));
                add_filter('wp_handle_upload_prefilter', array($this, 'changeFileName'));
                foreach ($files as $keyf => $file) {
					if(isset($postarr['save_variation_files']) && $postarr['save_variation_files']) {
						if($keyf == 'userfile') {
							$postWithoutFtp = $postarr;
							$this->digitalUploadVar($file, $postWithoutFtp, $key);
						}
						continue;
					}
                    $real_name = $file['name'];
					add_filter('upload_mimes', array($this, 'addUploadMimes'));
                    $upload = wp_handle_upload($file, array('test_form' => FALSE));
                    if (!empty($upload['type'])) {
                        if (isset($post_data['download_limit'][$key]) && is_numeric($post_data['download_limit'][$key])) {
                            $download_limit = $post_data['download_limit'][$key];
                        } else {
                            $download_limit = $params->download_limit;
                        }
                        if (isset($post_data['period_limit'][$key]) && is_numeric($post_data['period_limit'][$key])) {
                            $period_limit = $post_data['period_limit'][$key];
                        } else {
                            $period_limit = $params->period_limit;
                        }
                        if ($post_data['description'][$key] != '') {
                            $description = esc_sql($post_data['description'][$key]);
                        } else {
                            $description = $real_name;
                        }
                        $data_to_store = array(
                            'pid' => $post->ID,
                            'name' => $real_name,
                            'path' => $file['name'],
                            'mime_type' => $upload['type'],
                            'size' => $file['size'],
                            'date' => date('Y-m-d H:i:s'),
                            'active' => 1,
                            'description' => $description,
                            'download_limit' => $download_limit,
                            'period_limit' => $period_limit,
                        );
                        $product_files = frame::_()->getTable('product_files')->insert($data_to_store);
                    }
                    $key++;
				}
			}

			if(isset($post_data['exists']) && !empty($post_data['exists'])){
				foreach($post_data['exists'] as $item){
					$id = $item['id'];
					unset($item['id']);
					frame::_()->getTable('product_files')->update($item,$id);
				}
				unset($post_data['exists']);
			}
			if (!empty($post_data['product_ftp_urls'])) {
				/*
					there will be url validation if else statement
				*/
				$key = 0;
				if(isset($postarr['save_variation_files']) && $postarr['save_variation_files']) {
					$this->digitalUploadVar(false, $postarr, 0);
				} else {
					foreach($post_data['product_ftp_urls'] as $productFtpUrl) {
						$real_name = basename( $productFtpUrl );
						$path = $productFtpUrl;
						$mime = $this->get_mime_type($path, dirname(__FILE__));
						if ($mime) {
							if (isset($post_data['download_limit'][$key]) && is_numeric($post_data['download_limit'][$key])) {
								$download_limit = $post_data['download_limit'][$key];
							} else {
								$download_limit = $params->download_limit;
							}
							if (isset($post_data['period_limit'][$key]) && is_numeric($post_data['period_limit'][$key])) {
								$period_limit = $post_data['period_limit'][$key];
							} else {
								$period_limit = $params->period_limit;
							}
							if ($post_data['description'][$key] != '') {
								$description = esc_sql($post_data['description'][$key]);
							} else {
								$description = $real_name;
							}
							$fileNameData = $this->changeFileName(array('name' => $path));

							$uploadBaseDir = $this->changeUploadDir(wp_upload_dir());
							$uploadPath = $uploadBaseDir['path']. DS. $fileNameData['name'];

							if(@copy($path, $uploadPath)) {
								$path = $uploadPath;
								$headers  = @get_headers($path, 1);
								if(!empty($headers)){
									$fsize = $headers['Content-Length'];
								} else {
									if(file_exists($path)) {
										$fsize = filesize($path);
									} else {
										if(file_exists($uploadPath)) {
											$path = str_replace('\\',"/",$uploadPath);
											$fsize = filesize($uploadPath);
										} else {
											$fsize = 0;
										}
									}
								}
								$data_to_store = array(
									'pid' => $post->ID,
									'name' => $real_name,
									'path' => $fileNameData['name'],
									'mime_type' => $mime,
									'size' => $fsize,
									'date' => date('Y-m-d H:i:s'),
									'active' => 1,
									'description' => $description,
									'download_limit' => $download_limit,
									'period_limit' => $period_limit,
								);
								$product_files = frame::_()->getTable('product_files')->insert($data_to_store);
							}
						}
						$key++;
					}
				}
			}
			//exit();
            remove_filter('upload_dir', array($this, 'changeUploadDir'));
            remove_filter('wp_handle_upload_prefilter', array($this, 'changeFileName'));
            return $data;
        }

		
		public function digitalUploadVar($file, $postarr, $keyf) {
			global $post;
			$files = array();
			if(!empty($file)) {
				foreach($file['name'] as $key => $f) {
					foreach($f as $kname => $name) {
						$ff = array();
						$ff['name'] = $file['name'][$key][$kname];
						$ff['type'] = $file['type'][$key][$kname];
						$ff['tmp_name'] = $file['tmp_name'][$key][$kname];
						$ff['size'] = $file['size'][$key][$kname];
						$ff['post_id'] = $key;
						$files[] = $ff;
					}
				}
			}
            $post_data = $postarr;
            $params = frame::_()->getModule('digital_product')->getParamsObject();
            $key = $keyf;
            if(!empty($files)) {
                add_filter('upload_dir', array($this, 'changeUploadDir'));
                add_filter('wp_handle_upload_prefilter', array($this, 'changeFileName'));
                foreach ($files as $file) {
                    $real_name = $file['name'];
                    $upload = wp_handle_upload($file, array('test_form' => FALSE));
                    if (!empty($upload['type'])) {
                        if (isset($post_data['download_limit'][$key]) && is_numeric($post_data['download_limit'][$key])) {
                            $download_limit = $post_data['download_limit'][$key];
                        } else {
                            $download_limit = $params->download_limit;
                        }
                        if (isset($post_data['period_limit'][$key]) && is_numeric($post_data['period_limit'][$key])) {
                            $period_limit = $post_data['period_limit'][$key];
                        } else {
                            $period_limit = $params->period_limit;
                        }
                        if ($post_data['description'][$key]!='') {
                            $description = esc_sql($post_data['description'][$key]);
                        } else {
                            $description = $real_name;
                        }
                        $data_to_store = array(
                            'pid' => $file['post_id'],
                            'name' => $real_name,
                            'path' => $file['name'],
                            'mime_type' => $upload['type'],
                            'size' => $file['size'],
                            'date' => date('Y-m-d H:i:s'),
                            'active' => 1,
                            'description' => $description,
                            'download_limit' => $download_limit,
                            'period_limit' => $period_limit,
                        );
                        $product_files = frame::_()->getTable('product_files')->insert($data_to_store);
                    }
                    $key++;
				}
			}
			if (!empty($post_data['product_ftp_urls']) && empty($file)) {
				$key = 0;
                for ($i = 0; $i < count($post_data['product_ftp_urls']); $i++) {
                    $real_name = basename($post_data['product_ftp_urls'][$i]);
					$path = $post_data['product_ftp_urls'][$i];
					$mime = $this->get_mime_type($path, dirname(__FILE__));
                    if ($mime) {
                        if (isset($post_data['download_limit'][$key]) && is_numeric($post_data['download_limit'][$key])) {
                            $download_limit = $post_data['download_limit'][$key];
                        } else {
                            $download_limit = $params->download_limit;
                        }
                        if (isset($post_data['period_limit'][$key]) && is_numeric($post_data['period_limit'][$key])) {
                            $period_limit = $post_data['period_limit'][$key];
                        } else {
                            $period_limit = $params->period_limit;
                        }
                        if ($post_data['description'][$key] != '') {
                            $description = esc_sql($post_data['description'][$key]);
                        } else {
                            $description = $real_name;
                        }
						$fileNameData = $this->changeFileName(array('name' => $path));
						
						$uploadBaseDir = $this->changeUploadDir(wp_upload_dir());
						$uploadPath = $uploadBaseDir['path']. DS. $fileNameData['name'];

						if(@copy($path, $uploadPath)) {
							$path = $uploadPath;
							$headers  = @get_headers($path, 1);
							if(!empty($headers)){
								$fsize = $headers['Content-Length'];
							} else {
								if(file_exists($path)) {
									$fsize = filesize($path);
								} else {
									if(file_exists($uploadPath)) {
										$path = str_replace('\\', DS, $uploadPath);
										$fsize = filesize($uploadPath);
									} else {
										$fsize = 0;
									}
								}
							}
							$data_to_store = array(
								'pid' => $postarr['save_variation_files'],
								'name' => $real_name,
								'path' => $fileNameData['name'],
								'mime_type' => $mime,
								'size' => $fsize,
								'date' => date('Y-m-d H:i:s'),
								'active' => 1,
								'description' => $description,
								'download_limit' => $download_limit,
								'period_limit' => $period_limit,
							);
							$product_files = frame::_()->getTable('product_files')->insert($data_to_store);
						}
					}
                    $key++;
				}
			}
            remove_filter('upload_dir', array($this, 'changeUploadDir'));
            remove_filter('wp_handle_upload_prefilter', array($this, 'changeFileName'));
        }

        /**
         * Server File For User agent
         * @param array $file 
         */
        private function serveFile($file) {
            if (!empty($file['id'])){
                header("Content-type: ".$file['mime_type']);
                // lem9 & loic1: IE need specific headers
                if (PMA_USR_BROWSER_AGENT == 'IE') {
					header('Content-Disposition: inline; filename="'.$file['name'].'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
                } else {
					header('Content-Disposition: attachment; filename="'.$file['name'].'"');
					header('Expires: 0');
					header('Pragma: no-cache');
                }
				$upload_dir = wp_upload_dir();
                $path = $upload_dir['basedir']. DS. 'product_downloads';
                $path .= DS. $file['path'];
                ob_clean();   // discard any data in the output buffer (if possible)
                flush();      // flush headers (if possible)
                readfile($path);
                die();
            }
        }
        /**
         * Create token for file download
         * 
         * @param int $order_id
         * @param array $file
         * @param int $user_id 
         */
        private function createToken($order_id, $file, $user_id) {
            $token = md5($order_id.$file['path'].$user_id.$this->makeString(5).$file['id']);
            return $token;
        }
        
        /**
		 * Function to get MIME type
		 */
		 public function get_mime_type($filename, $mimePath = '') { 
			$fileext = substr(strrchr($filename, '.'), 1); 
			if (empty($fileext)) return (false); 
			$regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i"; 
			$lines = file("$mimePath/". $this->_mimeTypeFile); 
			foreach($lines as $line) { 
			  if (substr($line, 0, 1) == '#') continue; // skip comments 
			  $line = rtrim($line) . " "; 
			  if (!preg_match($regex, $line, $matches)) continue; // no match to the extension 
			  return ($matches[1]); 
			} 
			return (false); // no match at all 
		} 
        /**
         * Function to serve file to administrator
         */
        public function digitalDownload() {

            $administrator = current_user_can('administrator');
            if (!$administrator){
                die(lang::_e("You don't have permissions to access this page"));
            }
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = $_GET['id'];
                $conditions = array(
                    'id' => $id,
                );
                $items = frame::_()->getTable('product_files')->get('*',$conditions);
                $file = $items[0];
				// oute($file);
				
                $this->serveFile($file);
            }
            die(0);
        }
        protected function _die($error) {
            redirect(uri::_(array('toeErrors' => $error)));
        }
        /**
         * Provides the ability for users to download digital products
         * 
         * @return file 
         */
        public function digitalFileDownload(){
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = $_GET['id'];
            } else {
                $this->_die(lang::_('There is no such file'));
            }
            if (!isset($_GET['token'])) {
                $this->_die(lang::_('You are not allowed to access this page'));
            } else {
                $token = $_GET['token'];
            }
            $items = frame::_()->getTable('user_files')->get('*',array('id'=>$id));
            if (!$items) {
                die(lang::_('You are not allowed to access this page'));
            } else {
                $user_file = $items[0];
            }
            $current_date = time();
            $file_date = strtotime($user_file['expires']);
            $error = '';
            if ($user_file['token'] !== $token || ($current_date > $file_date && $file_date != 0)) {
                $this->_die(lang::_('The link to this page has expired'));
            } else {
                $item = frame::_()->getTable('product_files')->get('*', array('id'=>$user_file['fid']));
                if (!empty($item)) {
                    $file = $item[0];
                    // change token and decriment downloads
                    $token = $this->createToken($user_file['order_id'], $file, $user_file['user_id']);
                    $downloads = $user_file['downloads']?--$user_file['downloads']:0;
                    $data = array('token' => $token, 'downloads' => $downloads);
                    $change = frame::_()->getTable('user_files')->update($data, array('id'=>$id));
                    if ($change){
                        $this->serveFile($file);
                    }
                } else {
                    $this->_die(lang::_('There is no such file'));    
                }
            }
            die();
        }
        /**
         * Deletes Product File
         */
        public function digitalDelete(){
            $administrator = current_user_can('administrator');
            if (!$administrator){
                die(lang::_e("You don't have permissions to access this page"));
            }
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                $conditions = array(
                    'id' => $id,
                );
                $items = frame::_()->getTable('product_files')->get('*',$conditions);
                $file = $items[0];
                $delete = frame::_()->getTable('product_files')->delete($conditions);
                $upload_dir = wp_upload_dir();
                $path = $upload_dir['basedir'].DS.'product_downloads';
                $path .= DS.$file['path'];
                if ($delete) {
                    @unlink($path);
                }
            }
            die();
        }
        /**
         * Changes the Upload Dir destination
         * @param array $uploads
         * @return array 
         */
        public function changeUploadDir($uploads) {
            $uploads['subdir'] = 'product_downloads';
            $uploads['path'] = $uploads['basedir']. DS. $uploads['subdir'];
			$uploads['url'] = $uploads['baseurl']. '/'. $uploads['subdir'];
            return $uploads;
        }
        /**
         * Change the file name before uploading
         * 
         * @param array $file 
         */
        public function changeFileName($file) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file['name'] = $this->createFileName(). '.'. $ext;
            return $file;
        }
        /**
         * Creates random file name
         * @return string 
         */
        private function createFileName() {
            return $this->makeString().'-'.$this->makeString().'-'.$this->makeString().'-'.$this->makeString();
        }
      /**
       * Make random string
       *
       * @param integer $length
       * @param string $allowed_chars
       * @return string
       */
      private function makeString($length = 10, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {
        $result = '';
        $allowed_chars_len = strlen($allowed_chars);

        while(strlen($result) < $length) {
          $result .= substr($allowed_chars, rand(0, $allowed_chars_len), 1);
        } // for

        return $result;
      } // make_string
      /**
       * Function to create the user digital files 
       * 
       * @param array $order 
       */
      public function createUserFile($order) {
          $files = array();
          $user_id = $order['user_id'];
          $order_id = $order['order_id'];
          $products = $order['products'];
          if (!empty($products)) {
              foreach ($products as $product) {
                  $digital_products = frame::_()->getModule('digital_product')->getModel('downloads')->getProductDownloads($product['product_id']);
                  if (!empty($digital_products)) {
                      foreach ($digital_products as $digital_product) {
                          $files[] = $digital_product;
                      }
                  }
              }
          }
          // check if there are any files attached to a product
          if (!empty($files)) {
              // create user file links
              $links = $this->saveUserFiles($files, $user_id, $order_id);
              // check if the file is allowed to download and link successfully created
              if (!empty($links)) {
                  $user = get_user_by('id', $user_id);
                  if ($user) {
                    $this->sendNotification($user, $order, $links);
                  }
              }
          }
      }
      /**
       * Prepare variables to send the notification
       * 
       * @param object $user
       * @param array $order
       * @param array $links 
       */
      public function sendNotification($user, $order, $links) {
          $user_name = $user->data->display_name;
          $mail_to = $user->data->user_email;
          $products = '<ol>';
          if (!empty($order['products'])) {
            foreach ($order['products'] as $product) {
                $products .= '<li>'.$product['product_sku'].' '.$product['product_name'].'</li>';
            }
          }
          $products .= '</ol>';
          $rendered_links = '<ol>';
          if (!empty($links)) {
              foreach ($links as $link) {
                  $rendered_links .= '<li><a href="'.$link['link'].'">'.$link['name'].'</a><p>'.$link['description'].'</p></li>';
              }
          }
          $rendered_links .= '</ol>';
          $store_name = frame::_()->getModule('options')->getModel('options')->get(array('code'=>'store_name'));
          $variables = array(
              'user_name' => $user_name,
              'products' => $products,
              'links' => $rendered_links,
              'store_name' => $store_name,
              'order_id' => $order['order_id'],
          );
          frame::_()->getModule('messenger')->getController()->sendNotification($mail_to, 'digital_product', 'links_list', $variables);
      }
      /**
       * Saves the user files to database
       * 
       * @param array $files
       * @param int $user_id
       * @param int $order_id 
       * @return array $download_links
       */
      public function saveUserFiles($files, $user_id, $order_id) {
          $download_links = array();
          if (!empty($files)) {
              foreach ($files as $file) {
                  //check for expiration
                  $period_limit = $file['period_limit']*24*3600;
                  if ($period_limit == 0) {
                      $expiration_date = NULL;
                  } else {
                      $expiration_date = time()+$period_limit;
                  }
                  // create token for download link
                  $token_string = $this->createToken($order_id, $file, $user_id);
                  $data = array(
                      'fid' => $file['id'],
                      'uid' => $user_id,
                      'order_id' => $order_id,
                      'downloads' => $file['download_limit'],
                      'expires' => date("Y-m-d H:i:s",$expiration_date),
                      'token' => $token_string,
                  );
                  $user_file = frame::_()->getModule('digital_product')->getModel('user_downloads')->post($data);
                  if ($user_file) {
                      $download_links[] = array(
                            'link' => admin_url('admin-ajax.php?action=digital_file_download&id='.$user_file.'&token='.$token_string),
                            'name' => $file['name'],
                            'description' => $file['description'],
                          );
                  }
              }
          }
          return $download_links;
      }
      /**
       * Get My Downloads page
       */
      public function getDownloadsList() {
          add_filter('the_content', array($this->getView('downloads'), 'getAccountDownloads'));
      }
      public function replaceFile() {
          $res = new response();
          $fid = (int) req::getVar('fid');
          if(!empty($fid)) {
              $fileData = frame::_()->getTable('product_files')->getById($fid);
              if(!empty($fileData)) {
                $file = current($_FILES);
                $uploader = toeCreateObj('fileuploader', array());
                if($uploader->validate(key($_FILES), 'product_downloads', $fileData['path'])) {
                    $uploadDir = wp_upload_dir();
                    utils::deleteFile( $uploadDir['basedir']. DS. 'product_downloads'. DS. $fileData['path']);
                    $uploader->upload(array('ignore_db_insert' => true));
                    $data_to_store = array(
                        'name' => $file['name'],
                        'mime_type' => $file['type'],
                        'size' => $file['size'],
                        'date' => date('Y-m-d H:i:s'),
                    );
                    if($fileData['name'] == $fileData['description'])
                        $newDescription = $data_to_store['description'] = $file['name'];
                    else
                        $newDescription = $fileData['description'];
                    frame::_()->getTable('product_files')->update($data_to_store, $fid);
                    $res->addMessage(lang::_('File was updated'));
                    $res->addData(array('fid' => $fid, 'newDescription' => $newDescription));
                } else
                    $res->pushError($uploader->getError());
              } else 
                  $res->pushError(lang::_('File is missing in database'));
          } else
              $res->pushError(lang::_('Invalid file ID'));
          $res->ajaxExec();
      }
}