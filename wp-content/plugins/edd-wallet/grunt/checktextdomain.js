// https://github.com/stephenharris/grunt-checktextdomain
module.exports = {
	options: {
		text_domain: '<%= pkg.pot.textdomain %>',
		correct_domain: true,
		keywords: [
			'__:1,2d',
			'_e:1,2d',
			'_x:1,2c,3d',
			'esc_html__:1,2d',
			'esc_html_e:1,2d',
			'esc_html_x:1,2c,3d',
			'esc_attr__:1,2d',
			'esc_attr_e:1,2d',
			'esc_attr_x:1,2c,3d',
			'_ex:1,2c,3d',
			'_n:1,2,4d',
			'_nx:1,2,4c,5d',
			'_n_noop:1,2,3d',
			'_nx_noop:1,2,3c,4d',
			' __ngettext:1,2,3d',
			'__ngettext_noop:1,2,3d',
			'_c:1,2d',
			'_nc:1,2,4c,5d'
		]
	},
	files: {
		expand: true,
		src: [
			'**/*.php', // Include all files
			'!node_modules/**', // Exclude node_modules/
			'!grunt/**', // Exclude grunt files/
			'!build/**', // Exclude build folder/

		]
	}
};
