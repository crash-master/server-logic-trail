Module for SLT framework
Version 0.1 beta
This module can manage universal options. Example settings, system options, meta info and ...

Exist function unioption() or u can use model('\Modules\UniOption\Models\UniOption')
Methods exists: 
	unioption() -> set_option($option_name, $value, [$section_name], [$about_option]); OR unioption() -> set_option_from_arr($array);
	unioption() -> get_option($option_name); (return option, but only value field)
	unioption() -> get_by_name($option_name); (return option in full_array)
	unioption() -> get_by_id($option_id); (return option in full_array)

	unioption() -> get_by_name_value($option_name); (return option, but only value field)
	unioption() -> get_by_id_value($option_id); (return option, but only value field)

	unioption() -> get_by_section_name($section_name); (return all option with {$section_name})

	unioption() -> get_all(); (return all option from db)
