# AdvancedForm

# Work in progress: This is under development and is not perfect. The API is not backwards compatible.

## Overview

Multi-functional and easy-to-use form framework

It was created with the goal of a highly convenient API. No more worrying about forms!

## Features

- [ ] Full validation of form (security)
- [x] Supports class inheritance style / callback style
- [x] Supports method chain style
- [x] Custom handler buttons/elements for DRY
- [ ] Display the message in the form
- [ ] Highlight the elements of custom form
- [ ] Keep content when resend form
- [ ] Form back support (Easily install the back button/toggle)
- [x] Numerical validation of Input
- [x] Strict type support
- [x] Named buttons/elements (Easy to change order)

## How to use

### Class inheritance style

You can create a constructor for every class inheritance style form.

- `__construct`: the initialization process of the form

Arguments to pass to create

- `title`: Form title

#### CustomForm

- `elements`: Form elements (optional)

```php
public function __construct(string $title, array $elements = [])
```

- `handleSubmit`: Called when the form is submitted (optional)
- `handleClose`: Called when the form is closed (optional)

```php
use pjz9n\advancedform\custom\CustomForm;
use pjz9n\advancedform\custom\element\InputElement;
use pjz9n\advancedform\custom\element\ToggleElement;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;

class ExampleForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("This is title");
        $this
            ->appendElement(new InputElement("test input", name: "test_input"))
            ->appendElement(new ToggleElement("test toggle", name: "test_toggle"));
    }

    protected function handleSubmit(Player $player, CustomFormResponse $response): void
    {
        $player->sendMessage("input text: " . $response->getInputResult("test_input")->getText());
        $player->sendMessage("toggle value: " . ($response->getToggleResult("test_toggle")->getValue() ? "true" : "false"));
    }

    protected function handleClose(Player $player): void
    {
        $player->sendMessage("bye!");
    }
}
```

#### MenuForm

- `text`: Message text to display on the form
- `buttons`: List of selectable buttons (optional)

```php
public function __construct(string $title, string $text, array $buttons = [])
```

- `handleSelect`: Called when the button is selected
- `handleClose`: Called when the form is closed (optional)

```php
use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\button\image\ButtonImage;
use pjz9n\advancedform\button\image\ButtonImageTypes;
use pjz9n\advancedform\button\ImageButton;
use pjz9n\advancedform\menu\MenuForm;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pocketmine\player\Player;

class ExampleForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("This is title", "This is text");
        $this
            ->appendButton(new Button("button one", name: "one"))
            ->appendButton(new ImageButton("button two", new ButtonImage(ButtonImageTypes::PATH, "textures/items/apple"), name: "two"));
    }

    protected function handleSelect(Player $player, MenuFormResponse $response): void
    {
        $player->sendMessage(match ($response->getSelectedButton()->getName()) {
            "one" => "select one!",
            "two" => "select two!",
        });
    }

    protected function handleClose(Player $player): void
    {
        $player->sendMessage("bye!");
    }
}
```

#### ModalForm

- `text`: Message text to display on the form

```php
public function __construct(string $title, string $text, ?Button $yesButton = null, ?Button $noButton)
```

- `handleSelect`: Called when the button is selected

```php
use pjz9n\advancedform\modal\ModalForm;
use pjz9n\advancedform\modal\response\ModalFormResponse;
use pocketmine\player\Player;

class ExampleForm extends ModalForm
{
    public function __construct()
    {
        parent::__construct("This is title", "This is text");
    }

    protected function handleSelect(Player $player, ModalFormResponse $response): void
    {
        $player->sendMessage($response->isYesButton() ? "Select yes" : "Select no");
    }
}
```

### Callback style

Basically the same as the class inheritance style, but accepts closures as arguments.

It's easy to use, so it's useful for one-time forms.

#### CustomForm

Method signature

```php
use pjz9n\advancedform\custom\CallbackCustomForm;

public static function CallbackCustomForm::create(string $title, ?Closure $handleSubmit = null, ?Closure $handleClose = null, array $elements = []): CallbackCustomForm
```

Example code

```php
use pjz9n\advancedform\custom\CallbackCustomForm;
use pjz9n\advancedform\custom\element\InputElement;
use pjz9n\advancedform\custom\element\ToggleElement;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;

CallbackCustomForm::create("This is title", function (Player $player, CustomFormResponse $response): void {
    $player->sendMessage("input text: " . $response->getInputResult("test_input")->getText());
    $player->sendMessage("toggle value: " . ($response->getToggleResult("test_toggle")->getValue() ? "true" : "false"));
}, function (Player $player): void {
    $player->sendMessage("bye!");
})
    ->appendElement(new InputElement("test input", name: "test_input"))
    ->appendElement(new ToggleElement("test toggle", name: "test_toggle"));
```

#### MenuForm

Method signature

```php
use pjz9n\advancedform\menu\CallbackMenuForm;

public static function CallbackMenuForm::create(string $title, string $text, ?Closure $handleSelect = null, ?Closure $handleClose = null, array $buttons = []): CallbackMenuForm
```

Example code

```php
use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\button\image\ButtonImage;
use pjz9n\advancedform\button\image\ButtonImageTypes;
use pjz9n\advancedform\button\ImageButton;
use pjz9n\advancedform\menu\CallbackMenuForm;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pocketmine\player\Player;

CallbackMenuForm::create("This is title", "This is text", function (Player $player, MenuFormResponse $response): void {
    $player->sendMessage(match ($response->getSelectedButton()->getName()) {
        "one" => "select one!",
        "two" => "select two!",
    });
}, function (Player $player): void {
    $player->sendMessage("bye!");
})
    ->appendButton(new Button("button one", name: "one"))
    ->appendButton(new ImageButton("button two", new ButtonImage(ButtonImageTypes::PATH, "textures/items/apple"), name: "two"));
```

#### ModalForm

Method signature

```php
use pjz9n\advancedform\modal\CallbackModalForm;

public static function CallbackModalForm::create(string $title, string $text, ?Closure $handleSelect = null): CallbackModalForm
```

Example code

```php
use pjz9n\advancedform\modal\CallbackModalForm;
use pjz9n\advancedform\modal\response\ModalFormResponse;
use pocketmine\player\Player;

CallbackModalForm::create("This is title", "This is text", function (Player $player, ModalFormResponse $response): void {
    $player->sendMessage($response->isYesButton() ? "Select yes" : "Select no");
});
```