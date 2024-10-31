/* Function to submit rate for product
 * @param rate int rating number
 * @param pid int product ID
 **/
function toeRateProduct(rate, pid) {
    jQuery(this).sendForm({
        msgElID: 'toeRatesMsg'+ pid,
        data: {pid: pid, rate: rate, mod: 'rating', action: 'rateProduct', reqType: 'ajax'},
        onSuccess: function(res) {
            if(!res.error && res.data.rating) {
                toeSetRating(res.data.rating[0].rating, res.data.rating[0].pid);
            }
        }
    });
}
function toeSetRating(rate, pid, rateLinksBlock) {
    if(typeof(rateLinksBlock) == 'undefined')
        rateLinksBlock = jQuery('.toeRatesBlock'+ pid);     //for case when pid is not specified
    jQuery(rateLinksBlock).find('a').removeClass('toeSelectedRate').removeClass('toeHalfSelectedRate');
    for(var i = 1; i <= rate; i++) {
        jQuery(rateLinksBlock).find('.toeRatingLink'+ i).addClass('toeSelectedRate');
    }
    //Here i == next star, so lets check if we have more that half vote here
    if(Math.abs(i-rate) <= 0.5)
        jQuery(rateLinksBlock).find('.toeRatingLink'+ i).addClass('toeHalfSelectedRate');
}
function toeSetCommentRating(rate, rateLink) {
    jQuery(rateLink).parents('.toeRatesCommentBlock:first').find('input[name=toeRating]').val(rate);
    toeSetRating(rate, 0, jQuery(rateLink).parents('.toeRatesCommentBlock:first'));
}
jQuery(document).ready(function(){
    jQuery('.toeRating li a').not('.toeRateStarStatic').mouseover(function(){
        var rate = parseInt(jQuery(this).html());
        for(var i = 1; i <= rate; i++) {
            jQuery(this).parents('.toeRating:first').find('a.toeRatingLink'+ i).addClass('toeOverRate');
        }
    });
    jQuery('.toeRating li a').not('.toeRateStarStatic').mouseout(function(){
        jQuery(this).parents('.toeRating:first').find('a').removeClass('toeOverRate');
    });
});