/**
 * Adds employee deletion events to all elements with class deleteEmployee.
 * Elements need attribute rel=[employee id].
 */
$(document).ready(function() {
    /**
     * @param {int} id
     * @returns {undefined}
     */
    var delEmployee = function(id) {
        var ok = function(data, textStatus) {
            if ( data['status'] != 'ok' ) {
                if ( data.hasOwnProperty('message') )
                  alert("Sorry - the response from the server was: " + data['message']);
                else
                  alert("Sorry - deletion may have failed.");
            }
            else {
                $('.employeePartial[rel='+id+']').fadeOut();
            }
        }
        $.post("/delete_employee", {'id': id}, ok)
        .fail(function() {
            alert("Sorry - the request did not work.");
        });
    }
    
    $('.deleteEmployee').click(function() {
      delEmployee($(this).attr('rel'));
    });
});