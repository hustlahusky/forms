<div align="center">

# hustlahusky/forms

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
$ composer require hustlahusky/forms
```

## Usage

```php
use Hustlahusky\Forms\Builder\FormControlBuilder;
use Hustlahusky\Forms\Handler\FormHandler;
use Hustlahusky\Forms\Form;
use Hustlahusky\Forms\FormControlSize;
use Hustlahusky\Forms\Render;

$form = new Form();

$form->addControl(
    FormControlBuilder::text('first_name', 'First Name')
        ->setRequired()
        ->setSize(FormControlSize::new(6))
        ->build()
);
$form->addControl(
    FormControlBuilder::text('last_name', 'Last Name')
        ->setRequired()
        ->setSize(FormControlSize::new(6))
        ->build()
);
$form->addControl(
    FormControlBuilder::email('email', 'Email')
        ->setRequired()
        ->setSize(FormControlSize::new(6))
        ->build()
);
$form->addControl(
    FormControlBuilder::phone('phone', 'Phone')
        ->setSize(FormControlSize::new(6))
        ->build()
);
$form->addControl(
    FormControlBuilder::radio('radio', 'Radio Buttons')
        ->setReadonly()
        ->addOption('1')
        ->addOption('2')
        ->addOption('3')
        ->setValue('1')
        ->build()
);
$form->addControl(
    FormControlBuilder::select('select')
        ->setReadonly()
        ->addOption('1')
        ->addOption('2')
        ->addOption('3')
        ->setValue('1')
        ->build()
);
$form->addControl(
    FormControlBuilder::select('select_multi')
        ->setReadonly()
        ->setMultiple()
        ->addOption('1')
        ->addOption('2')
        ->addOption('3')
        ->setValue(['1', '2'])
        ->build()
);
$form->addControl(
    FormControlBuilder::checkbox('checkbox')
        ->setReadonly()
        ->setValue(true)
        ->build()
);
$form->addControl(
    FormControlBuilder::checkboxMulti('checkbox_multi')
        ->setReadonly()
        ->addOption('1')
        ->addOption('2')
        ->addOption('3')
        ->setValue(['1', '2'])
        ->build()
);

$render = new Render\BootstrapFormRenderer($form, [
    Render\BootstrapFormRenderer::FORM_ATTRS => [
        'class' => 'w-100',
    ],
    Render\BootstrapFormRenderer::FIELDSET_ATTRS => [
        'class' => 'my-3',
    ],
    Render\BootstrapFormRenderer::LEGEND_ATTRS => [
        'class' => 'h3',
    ],
    Render\BootstrapFormRenderer::ROW_ATTRS => [
        'class' => 'my-2',
    ],
]);

function output(iterable $iter): void
{
    foreach ($iter as $item) {
        echo $item, \PHP_EOL;
    }
}

output($render->startForm());
output($render->renderControls());
output($render->submitButton());
output($render->endForm());


if ('post' === \strtolower($_SERVER['REQUEST_METHOD'])) {
    $handler = new FormHandler($form);
    $data = $handler->handle($_POST);
}
```

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[link-github]: https://github.com/hustlahusky/forms
[link-packagist]: https://packagist.org/packages/hustlahusky/forms
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
