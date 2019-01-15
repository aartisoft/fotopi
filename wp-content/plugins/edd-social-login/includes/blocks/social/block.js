(function (blocks, editor, components, i18n, element) {
  var el                = element.createElement
  var registerBlockType = blocks.registerBlockType
  var BlockControls     = editor.BlockControls
  var InspectorControls = editor.InspectorControls
  var TextControl       = components.TextControl
  var CheckboxControl   = components.CheckboxControl
  
  var ServerSideRender  = components.ServerSideRender

  // custom block icon
  const iconEl = el('svg', { width: 20, height: 20 },
    el('path', { d: "M12.5,12H12v-0.5c0-0.3-0.2-0.5-0.5-0.5H11V6h1l1-2c-1,0.1-2,0.1-3,0C9.2,3.4,8.6,2.8,8,2V1.5C8,1.2,7.8,1,7.5,1 S7,1.2,7,1.5V2C6.4,2.8,5.8,3.4,5,4C4,4.1,3,4.1,2,4l1,2h1v5c0,0-0.5,0-0.5,0C3.2,11,3,11.2,3,11.5V12H2.5C2.2,12,2,12.2,2,12.5V13 h11v-0.5C13,12.2,12.8,12,12.5,12z M7,11H5V6h2V11z M10,11H8V6h2V11z" } )
  );

  const { __ } = i18n;

  // register social login custom block
  registerBlockType('wpweb/edd-social-login-block', {
    title: 'Easy Digital Downloads Social Login', 
    description: 'Display Easy Digital Downloads Social Login shortcode as block using the Gutenberg editor.',
    icon: 'networking',
    category: 'widgets',
    attributes: { // Necessary for saving block content.
      title: {
        type    : 'text',
        default : i18n.__('Prefer to Login with Social Media')
      },
      redirect_url: {
        type    : 'url',
        default : ''
      },
      showonpage: {
        default : false
      }
    },

    edit: function (props) {
      
      var attributes      = props.attributes
      var title           = props.attributes.title
      var redirect_url    = props.attributes.redirect_url
      var showonpage      = props.attributes.showonpage

      return [
        el(BlockControls, { key: 'controls' }, // Display controls when the block is clicked on.
        ),
        el(InspectorControls, { key: 'inspector' }, // Display the block options in the inspector panel.
          el(components.PanelBody, {
            title: i18n.__('EDD Social Login Settings'),
            className: 'wp-block-settings',
            initialOpen: true
          },
          // Social Login Title
          el(TextControl, {
            type  : 'text',
            label : i18n.__('Social Login Title'),
            help  : i18n.__('Enter a social login title.'),
            value : title,
            onChange: function (newContent) {
              props.setAttributes({ title: newContent })
            }
          }),
          // Redirect url
          el(TextControl, {
            type  : 'url',
            label : i18n.__('Redirect URL'),
            help  : i18n.__('Enter a redirect URL for users after they login with social media. The URL must start with http:// or https://'),
            value : redirect_url,
            onChange: function (newRedirectURL) {
              props.setAttributes({ redirect_url: newRedirectURL })
            }
          }),
          // Show Only on Page / Post
          el(CheckboxControl, {
            label   : i18n.__('Show Only on Page / Post.'),
            help    : i18n.__('Check this box if you want to show social login buttons only on inner page of posts and pages.'),
            checked : showonpage,
            onChange: function (newCheck) {
              props.setAttributes({ showonpage: newCheck })
            }
          })
          )
        ),
        el(ServerSideRender, {
          block      : 'wpweb/edd-social-login-block',
          attributes : attributes,
        }),
      ]
    },

    save: function (props) {
      return null;      
    },
  } );
}(
  window.wp.blocks,
  window.wp.editor,
  window.wp.components,
  window.wp.i18n,
  window.wp.element
) );
