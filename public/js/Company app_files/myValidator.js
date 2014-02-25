/**
 * 
 * @returns {myValidator}
 */
var myValidator0 = function()
{
  this.url = null; // string url
  this.form = null; // form object
  
  this.validate = function(inputName, value) {
    $.ajax({
      type: "POST",
      url: this.url,
      data: {
        propName: inputName,
        value: value
      }
    });
  };
  
  /**
   * Adds a change event on form input.
   * @param {string} fieldname
   * @returns {undefined}
   */
  this.setValidation = function(fieldname) {
    var input = this.form.find('[name='+fieldname+']').first(); 
    var validate = this.validate;
    var me = this;
    input.keyup(function(){
      validate.call(me, fieldname, input.val());
    });
  };
};