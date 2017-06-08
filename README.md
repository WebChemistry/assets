## Registration
```neon
extensions:
	assets: WebChemistry\Assets\DI\AssetsExtension
	
assets:
	resources:
		- %appDir%/assets.neon
```

## Assets config
Example in app dir create assets.neon:
```yaml
## Front module
front:
  css:
    "compiled/front.min.css":
      - "css/*.css"
      - "plugins/bootstrap/css/bootstrap.css"
      - "plugins/forms/css/jquery.datetimepicker.css"
      - "plugins/forms/css/selectize.css"
      - "plugins/forms/css/selectize.default.css"
  js:
    "compiled/front.min.js":
      - "plugins/nette/netteForms.js"
      - "plugins/nette/nette.ajax.js"
      - "plugins/nette/extensions/spinner.ajax.js"
      - "plugins/jquery-nette-forms/libraries.js"
      - "plugins/jquery-nette-forms/errors.js"
      - "plugins/forms/js/inputmask.min.js"
      - "plugins/forms/js/inputmask.regex.extensions.min.js"
      - "plugins/forms/js/jquery.inputmask.min.js"
      - "plugins/forms/js/jquery.datetimepicker.js"
      - "plugins/forms/js/selectize.min.js"
      - "plugins/bootstrap/js/bootstrap.js"
      - "js/main.js"
    "compiled/front.header.min.js":
      - "plugins/jquery/jquery.min.js"
```

## Presenter

```php

class BasePresenter extends Nette\Application\UI\Presenter {

	use WebChemistry\Assets\TPresenter;
	
}
```

## Template

```html
<head>
	{$assets->getCss('compiled/front.min.css')} <!-- Debug mode: all css files, production: only minified -->
	{$assets->getJs('compiled/front.head.min.js')}
</head>
```

## Grunt task

package.json
```json
{
  "name": "Project",
  "version": "1.0.0",
  "devDependencies": {
    "grunt": "~0.4.5",
    "grunt-contrib-cssmin": "~0.14.0",
    "grunt-contrib-uglify": "~0.11.1",
    "grunt-nette-assets": "~0.1.0"
  },
  "dependencies": {}
}
```

Install:
```
npm install
```

Gruntfile.js:
```js
module.exports = function(grunt) {
    grunt.config.init({
        netteAssets: {
            target: {
                config: 'app/resource.neon',
                basePath: 'www/'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-nette-assets');

    return grunt.registerTask('default', ['netteAssets', 'uglify', 'cssmin']);
};
```

Run grunt:
```
grunt
```