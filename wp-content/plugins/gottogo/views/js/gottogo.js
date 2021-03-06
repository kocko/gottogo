function switchBetweenCollapsibleDivs(showId, hideId) {
    jQuery('#' + showId).collapse('show');
    jQuery('#' + hideId).collapse('hide');
}

function validatePassword(first, second) {
    var password = document.getElementById(first);
    var confirm_password = document.getElementById(second);

    if (password.value != confirm_password.value) {
        confirm_password.setCustomValidity("Паролите не съвпадат!");
    } else {
        confirm_password.setCustomValidity('');
    }
}

function validateEmail(componentId, errorMessageContainerId, successResult, disableButtonFunction, formButtonId) {
    var email = jQuery('#' + componentId).val();
    if (email) {
        jQuery.post("wp-content/plugins/gottogo/views/utils/check_email.php", {register_email: email},
            function (result) {
                disableButtonFunction(result, formButtonId);
                if (result == successResult) {
                    jQuery('#' + errorMessageContainerId).show();
                } else {
                    jQuery('#' + errorMessageContainerId).hide();
                }
            }
        );
    }
}
