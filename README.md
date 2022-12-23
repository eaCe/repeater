# REDAXO-Addon: Repeater

## :construction: ACHTUNG: ALPHA-Version - Bitte nur zum Testen benutzen.

REDAXO 5 Addon für eine einfache Wiederholung von Inhalten innerhalb eines Moduls.

Die Inhalte können auf 2 Ebenen verschachtelt werden.

Alle Felder benötigen einen Namen. Ein zweiter Parameter ermöglicht es eine Feldbreite in Prozent anzugeben.

![Screenshot](https://raw.githubusercontent.com/eaCe/repeater/assets/repeater-alpha.jpg)

Modul-Input:
```php
// Repeater mit einer REX_VALUE-ID von 1 instanziieren
$repeater = new repeater(1);

// Gruppe anlegen (Wrapper des Repeaters)
$repeater->addGroup('Wrapper Gruppe');

// Textfeld mit 60% Breite zu "Wrapper Gruppe"  hinzufügen
$repeater->addText('Titel', 60);

// Textarea mit 40% Breite zu "Wrapper Gruppe" hinzufügen
$repeater->addTextArea('Text', 40);

// Verschachtelte Gruppe anlegen
$repeater->addGroup('Verschachtelte Gruppe');

// Link zu "Verschachtelte Gruppe"  hinzufügen
$repeater->addLink('Link');

// Link zu "Verschachtelte Gruppe"  hinzufügen
$repeater->addMedia('Bild');

// Repeater rendern
$repeater->show();
```

Modul-Output:
```php
$repeaterItems = json_decode(html_entity_decode(REX_VALUE[1]));
```
