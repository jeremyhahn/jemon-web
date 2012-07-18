/**
 * Initialize the application once the DOM is loaded
 */
$(document).ready(function() {

	// Hide the "Generating report..." message on page load
	$('#loading-bar').hide();

	// Set up the first date range datetimepicker component
	$('#date-range-1').datetimepicker({
	    onClose: function(dateText, inst) {
	        var endDateTextBox = $('#example16_end');
	        if (endDateTextBox.val() != '') {
	            var testStartDate = new Date(dateText);
	            var testEndDate = new Date(endDateTextBox.val());
	            if (testStartDate > testEndDate)
	                endDateTextBox.val(dateText);
	        }
	        else {
	            endDateTextBox.val(dateText);
	        }
	    },
	    onSelect: function (selectedDateTime){
	        var start = $(this).datetimepicker('getDate');
	        $('#example16_end').datetimepicker('option', 'minDate', new Date(start.getTime()));
	    }
	});

	// Set up the second date range datetimepicker component
	$('#date-range-2').datetimepicker({
	    onClose: function(dateText, inst) {
	        var startDateTextBox = $('#example16_start');
	        if (startDateTextBox.val() != '') {
	            var testStartDate = new Date(startDateTextBox.val());
	            var testEndDate = new Date(dateText);
	            if (testStartDate > testEndDate)
	                startDateTextBox.val(dateText);
	        }
	        else {
	            startDateTextBox.val(dateText);
	        }
	    },
	    onSelect: function (selectedDateTime){
	        var end = $(this).datetimepicker('getDate');
	        $('#example16_start').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
	    }
	});

	/* Bind event handlers */

	// The onChange event handler for "Reports" fieldset. Shows the loading
	// bar and dispatches a synchronous request to the backend to generate
	// the report / graph. Hides the loading bar when the response is received.
	$('#cannedReport').change(function (e) {

		var selected = $(this).find(":selected").val();

		$('#loading-bar').show();
		$("#center-image").attr("src", "/view/images/blank.gif");
	
		var xhr = new AgilePHP.XHR();
		    xhr.setSynchronous(true);
		    xhr.request('/index.php/IndexController/report/' + selected + '/' + selected);

		$("#center-image-link").attr("href", "/generated/jemon-graph-report-big.png");
		$("#center-image").attr("src", "/generated/jemon-graph-report-center.png");

		$('#loading-bar').hide();
	});

	// Performs a cusom date / time search. Shows the loading bar and dispatches
	// a synchronous request to the backend to generate the report / graph. Hides
	// the loading bar when the response is received.
	$('#date-range-button').click(function() {

		var start = $('#date-range-1').datepicker('getDate');
		var end = $('#date-range-2').datepicker('getDate');

		$('#loading-bar').show();
		$("#center-image").attr("src", "/view/images/blank.gif");

		var xhr = new AgilePHP.XHR();
		    xhr.setSynchronous(true);
		    xhr.request('/index.php/IndexController/report/' + start + '/' + end);

	    $("#center-image-link").attr("href", "/generated/jemon-graph-report-big.png");
		$("#center-image").attr("src", "/generated/jemon-graph-report-center.png");

		$('#loading-bar').hide();
	});
});