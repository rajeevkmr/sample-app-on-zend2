/**
 * JS validator for Company app forms. Example usage:
 * 
var validator = new myValidator({
  'url':'/companies/validateJSON',
  'form': $('#company'),
});
validator.setInput('name').setValidation();

 * the server needs to return JSON status=ok|error,message={string}
 * the default callback on error displays an <ul> list in sibling element of
 * the input with a class .error.
 * 
 * @param {type} params
 * @returns {myValidator}
 */
var myValidator = function(params)
{
  var p = params ? params : {};
  /**
   * @var {string} validator url
   */
  this.url = p.hasOwnProperty('url') ? p['url'] : null;
  
  /**
   * @var {object} form object
   */
  this.form = p.hasOwnProperty('form') ? p['form'] : null;
  
  /**
   * @var {object} input object to validate
   */
  this.input = p.hasOwnProperty('input') ? p['input'] : null; 
  
  /**
   * Callback when input validation fails
   * @param {string} message HTML list with messages.
   */
  this.errorCallback = function(message) {
    var errorContainer = this.input.siblings('.errors').first();
    errorContainer.html(message);
    this.input.after(errorContainer);
  }
  
  /**
   * Callback when input validation succeeds
   * @param {string} message HTML list with messages.
   */
  this.okCallback = function() {
    var errorContainer = this.input.siblings('.errors').first();
    if ( errorContainer.length ) {
      errorContainer.empty();
    }
  }
  
  /**
   * Execute validation.
   * @returns {undefined}
   */
  this.validate = function(inputName, value) {
    var errorCallback = this.errorCallback;
    var okCallback = this.okCallback;
    var me = this;
    $.ajax({
      type: "POST", // POST as it allows for longer values
      url: this.url,
      data: {
        propName: inputName,
        value: value
      }
    })
    .error(function(){}) // do nothing. server will validate
    .success(function(data){
        var status = data.hasOwnProperty('status') ? data['status'] : null;
        var errMsgs = data.hasOwnProperty('message') ? data['message'] : null;
        //console.log(status);
        if ( status == 'ok' ) {
          okCallback.call(me);
          return;
        }

        if ( status == 'error' && errMsgs ) {
          // the below should be replaced with a call for a html list generator
          var msg = '<ul>';
          if ( $.type(errMsgs) === 'string' ) {
            msg += '<li>'+errMsgs+'</li>';
          }
          else {
            for ( var prop in errMsgs ) {
                if ( errMsgs.hasOwnProperty(prop) ) {
                    msg += '<li>'+errMsgs[prop]+'</li>';
                }
            }
          }
          msg += '</ul>';
          errorCallback.call(me, msg);
          return;
        }
        // AJAX validation has failed - dont call the callback. Let the server validate.
     });
  };
  
  /**
   * Sets this.input to the first found form element of this form.
   * @param {string} name
   * @returns {this}
   */
  this.setInput = function(name) {
    this.input = this.form.find('[name='+name+']').first(); 
    return this;
  };
  
  /**
   * Finalizes validation: adds a change event to the input.
   * @returns {undefined}
   */
  this.setValidation = function() {
    var validate = this.validate;
    var input = this.input;
    var inputName = input.attr('name');
    var me = this;
    input.change(function(){
      validate.call(me, inputName, input.val());
    });
  };
};