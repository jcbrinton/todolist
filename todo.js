	$(function () {
			
		//call for create and update
		
        $('form').on('submit', function (e) {
			
		    e.preventDefault();
		  
            $.ajax({
				type: 'post',
				url: 'update.php',
				data: $('form').serialize(),
				complete: function () {
				    location.reload(true);
				}
            });
		});
		
		//call for delete
		
		$('.delete').on('click', function (e) {
		
            $.ajax({
				type: 'post',
				url: 'update.php',
				data: $(this).attr('value')+'='+$(this).attr('id'),
				complete: function () {
					location.reload(true);
				}
			}); 
		});
		
		//autopopulate update modal with current info
		
		$('.update').on('click', function (e) {
			
			$("#identry").attr("value", $(this).attr('data-id'));
			$("#descriptionentry").attr("value", $(this).attr('data-description'));
			$("#duedateentry").attr("value", $(this).attr('data-duedate').toString());
			if($(this).attr('data-status')=='Complete')
				$("#statusselect [value='Complete']").prop('selected',true).trigger('change');
			else
				$("#statusselect [value='Pending']").prop('selected',true).trigger('change');
			
		});
		
		//autopopulate subtask modal with task id
		
		$('.addSubtask').on('click', function (e) {
			
			$("#subtaskentry").attr("value", $(this).attr('data-id'));
			
		});
		
		//toggle pending and complete checkboxes on click
		
		$("#pendingcheckbox").on('click', function (e) {
			
			$("tr[data-status=Pending]").toggle();
			
		});
		
		$("#completecheckbox").on('click', function (e) {
			
			$("tr[data-status=Complete]").toggle();
			
		});
		  
    }); 