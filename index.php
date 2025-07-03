<?php

use Kirby\Cms\App as Kirby;
use Kirby\Data\Data;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Form\Form;
use JanHerman\ButtonField\Button;

require_once 'lib/Button.php';

Kirby::plugin('jan-herman/button-field', [
    'fields' => [
        'button' => [
            'props' => [
                /**
                 * @values 'anchor', 'url, 'page, 'file', 'email', 'tel', 'custom'
                 */
                'options' => function (array|null $options = null): array {
                    return $options ?? [
                        'url',
                        'page',
                        'file',
                        'email',
                        'tel',
                        'anchor',
                        'custom'
                    ];
                },
                'fields' => function (array $fields = []) {
                    return $fields;
                }
            ],
            'computed' => [
                'fields' => function () {
                    if (empty($this->fields) === true) {
                        return [];
                    }

                    return $this->form()->fields()->toArray();
                },
                'value' => function () {
                    $data = Data::decode($this->value, 'yaml');

                    if (empty($data) === true) {
                        return [
                            'link' => '',
                            'text' => '',
                        ];
                    }

                    return $this->form($data)->values();
                }
            ],
            'methods' => [
                'form' => function (array $values = []) {
                    return new Form([
                        'fields' => $this->attrs['fields'],
                        'values' => $values,
                        'model'  => $this->model
                    ]);
                },
            ],
            'save' => function ($value) {
                if (empty($value) === true) {
                    return '';
                }

                if (is_array($value) === true && empty(array_filter($value)) === true) {
                    return '';
                }

                return $this->form($value)->content();
            },
            'validations' => [
                'object' => function ($value) {
                    if (empty($value) === true) {
                        return true;
                    }

                    $errors = $this->form($value)->errors();

                    if (empty($errors) === false) {
                        // use the first error for details
                        $name  = array_key_first($errors);
                        $error = $errors[$name];

                        throw new InvalidArgumentException([
                            'key'  => 'object.validation',
                            'data' => [
                                'label'   => $error['label'] ?? $name,
                                'message' => implode("\n", $error['message'])
                            ]
                        ]);
                    }
                }
            ]
        ]
    ],
    'fieldMethods' => [
        'toButton' => function ($field): Button {
            $props = $field->isNotEmpty() ? $field->yaml() : [];
            return new Button($props);
        }
    ],
    'translations' => [
        'en' => [
            'jan-herman.button-field.text'     => 'Text',
            'jan-herman.button-field.settings' => 'Settings',
            'jan-herman.button-field.delete'   => 'Delete',
        ],
        'cs' => [
            'jan-herman.button-field.text'     => 'Text',
            'jan-herman.button-field.settings' => 'Nastavení',
            'jan-herman.button-field.delete'   => 'Vymazat',
        ]
    ]
]);
