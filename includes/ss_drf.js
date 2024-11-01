$j = jQuery;
$j( document ).ready(function() {
$j('.drduplicate').click(function(e){e.preventDefault();
       var data = {
        "action": "my_ss_drf_duplicate",        
        "formid": $j(this).data('formid')
            };	
	      $j.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                dataType: "json",
                success: function (response) {     
                $j('.updated.fade').remove();           
                $j(response.message).insertAfter(".wrap h2");
                setTimeout(location.reload(), 2000);            
            }
         }); 	
});
});
$j( window ).load(function() {
$j('html').animate({scrollTop:0}, 1);
$j('body').animate({scrollTop:0}, 1);
});