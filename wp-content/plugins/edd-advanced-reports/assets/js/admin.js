jQuery(document).ready(function(){
    
	/* DATE PICKER FIELD */
	jQuery('.eddar-dateselector').datepicker({dateFormat: 'yy-mm-dd'});
	jQuery('.eddar-dateselector').each(function(){
	});
	
	//Add row in collection field
	jQuery('body').on('click', '.eddar-collection-add-row', function(e) {
		e.preventDefault();
		var current_element = jQuery(this);
		var row = current_element.parent().parent().prev('tr');
		var new_row = eddar_add_row(row);
		new_row.insertAfter(row);
	});
	
	//Remove row in collection field
	jQuery('body').on('click', '.eddar-collection-remove-row', function(e) {
		e.preventDefault();

		var row = jQuery(this).parent().parent('tr');
		var count = row.parent().find('tr').length - 1;
		var type  = jQuery(this).data('type');
		
		//Always leave at least one row
		if( count > 1 ) {
			jQuery('input, select', row).val('');
			row.remove();
		}

		//Reorder rows
		jQuery('.eddar-collection-row').each(function(rowIndex){
			jQuery(this).find('input, select').each(function(){
				var name = jQuery( this ).attr('name');
				name = name.replace(/\[(\d+)\]/, '[' + rowIndex + ']');
				jQuery(this).attr('name', name ).attr('id', name);
			});
		});
	});
	
});

function eddar_add_row(row){
	// Retrieve the highest current field index
	var key = highest = 1;
	row.parent().find('tr.eddar-collection-row').each(function(){
		var current = jQuery(this).data('index');
		if(parseInt(current) > highest){
			highest = current;
		}
	});
	key = highest += 1;
	
	new_row = row.clone();
	new_row.find('td input').val('');
	
	//Update index and names of new row
	new_row.data('index', key);
	new_row.find('input, select, textarea').each(function() {
		var new_name = jQuery(this).attr('name');
		new_name = new_name.replace(/\[(\d+)\]/, '[' + parseInt( key ) + ']');
		jQuery(this).attr('name', new_name).attr('id', new_name);
	});
	
	return new_row;
}