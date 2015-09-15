(function($) {
        $('select').selectric({
            optionsItemBuilder: function(itemData, element, index) {
                return element.val().length ? '<span class="ico ico-' + element.val() +  '"></span>' + itemData.text : itemData.text;
            }
        });



        $('.fr div').hover(function() {
            $('#userlogout').toggle();
        });


        

        $(document).click(function(e) {   
            if(e.target.id != 'notificationbox' && e.target.id != 'notificationFrontImage') {
                $("#notificationbox, .notificationarrow").hide();
                str = $("#notificationFrontImage").attr('src');
                str1 = str.substr(17);
                if (str1 != "9plus.png") {
                    str1 =  Number(str1.split('.')[0]) - 5;
                    if (str1 < 0) {
                        str1 = 0
                    }
                }
                $("#notificationFrontImage").attr('src', '/img/notification' + str1 + '.png');
            } 
        });
})(jQuery);