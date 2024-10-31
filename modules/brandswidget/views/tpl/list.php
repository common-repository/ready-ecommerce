<div class="toeWidget">
    <?php if(!empty($this->params['title'])) { ?>
        <h2 class="toeWidgetTitle"><?php lang::_e($this->params['title'])?></h2>
    <?php }?>
    
    <?php $terms = get_terms('products_brands'); ?>
    <?php switch ($this->params['list_view']) { 
          case 0:
        ?>
        <div id="brands">
            <div class="wrapper">
                <ul>
                    <?php 
                        foreach ($terms as $term) {  
                            $term_link = get_term_link((int)$term->term_id, 'products_brands');
                            $term_icon = get_metadata('products_brands', (int)$term->term_id, 'brand_thumb', true);
                            $timsettings_thumb = '&h=90&w=120&zc=2';
                            
                            switch ($this->params['img_size']) { 
                                case 0: $image_path = S_TIM_PATH.$term_icon.$timsettings_thumb; break;
                                case 1: $image_path = $term_icon; break;
                                default: break;
                            }
                            
                            echo "<li><a href='".$term_link."'><img src='".$image_path."' /></a></li>";
                        }
                    ?>
                </ul>
            </div>        
        </div><!-- End Brands -->
    <?php break; 
          case 1:  
        ?>
        <div class="widget">
            <div class="widget-body">
            <ul>
        <?php 
            foreach ($terms as $term) {  
                $term_link = get_term_link((int)$term->term_id, 'products_brands');
                echo "<li><a href='".$term_link."'>".$term->name."</a></li>";
            }
        ?>
            </ul>
            </div>
        </div>
    <?php break;
          default:
          break;
    } ?>
</div>