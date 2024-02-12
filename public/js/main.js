/* main.js */
$(document).ready(function() {
    //Initialize Select2 Elements
    $('.select2').select2({
      theme: 'bootstrap4'
    })

    // Inputmask
    $('.input_mask').inputmask()
})

/*HoldOn js config*/
var holdon_options = {
	theme: "sk-circle",
	message: "Loading... Please Wait",
	textColor: "white"
}
