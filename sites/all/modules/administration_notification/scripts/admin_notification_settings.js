Drupal.behaviors.admin_notification = function()
{
	$("#edit-email").val(Drupal.settings.admin_notification.email)
	$("#edit-email").bind("focus", function()
	{
		if($(this).val() == Drupal.settings.admin_notification.email)
		{
			var text = '';
			$(this).val(text)
		}
	})
	$("#edit-email").bind("blur", function()
	{
	  	if($(this).val() == '')
		{
			$(this).val(Drupal.settings.admin_notification.email)
		}
	})	
}