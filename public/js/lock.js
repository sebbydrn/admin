var idleTime = 0

$(document).ready(function() {
	// Check if is_locked is set to 1
	if (sessionStorage.getItem("is_locked") == 1) {
		// Show lockscreen
		$('.wrapper').css('display', 'none') // hide main wrapper
		$('.lockscreen-wrapper').css('display', 'block') // show lock screen
		$('body').removeClass('hold-transition sidebar-mini').addClass('hold-transition lockscreen"') // remove main body class and add lockscreen body class
	
		// Disable back button in browser
		history.pushState(null, null, location.href);
		window.onpopstate = function() {
			history.go(1)
		}
	}

	/*Detecting idle time*/
    // Increment the idle time counter every minute
    var idleInterval = setInterval(timerIncrement, 100000) // 1 minute

    // Zero the idle timer on mouse movement
    $(this).mousemove(function (e) {
    	idleTime = 0
    })
    $(this).keypress(function(e) {
    	idleTime = 0
    })

    /*Unlock*/
    $('#unlock').on('submit', function(e) {
    	e.preventDefault()
    	var password = $('#password').val()
        // Check if user is logged in
        $.ajax({
            type: 'GET',
            url: base_route + '/check_logged_in',
            dataType: 'json',
            success: function(source) {
                if (source == 1) {
                    $.ajax({
                        type: 'POST',
                        url: base_route + '/unlock',
                        data: {
                            _token: _token,
                            password: password
                        },
                        dataType: 'json',
                        success: function(source) {
                            // password matches
                            if (source == 1) {
                                $('.lockscreen-wrapper').css('display', 'none') // hide lock screen
                                $('.wrapper').css('display', 'block') // hide main wrapper
                                $('body').addClass('hold-transition sidebar-mini').removeClass('hold-transition lockscreen"') // add main body class and remove lockscreen body class
                                $('#password').val('') // empty password field
                                sessionStorage.removeItem("is_locked") // Set is_locked to 0
                            } else { // password did not match
                                Swal.fire({
                                    title: 'Oops!',
                                    text: "Incorrect password",
                                    icon: 'warning'
                                })
                            }
                        }
                    })
                } else {
                    sessionStorage.removeItem("is_locked") // Set is_locked to 0
                    location.reload()
                }
            }
        })
    	
    })
})

function timerIncrement() {
	idleTime = idleTime + 1
	if (idleTime > 9) { // 10 minutes
		$('.wrapper').css('display', 'none') // hide main wrapper
		$('.lockscreen-wrapper').css('display', 'block') // show lock screen
		$('body').removeClass('hold-transition sidebar-mini').addClass('hold-transition lockscreen"') // remove main body class and add lockscreen body class
	
		// Disable back button in browser
		history.pushState(null, null, location.href);
		window.onpopstate = function() {
			history.go(1)
		}

		// Set sessionStorage
		sessionStorage.setItem("is_locked", 1)
	}
}

function remove_lock_screen() {
    sessionStorage.removeItem("is_locked") // Set is_locked to 0
}