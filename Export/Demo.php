<?
require_once 'src/Export.php';
require_once '../Ao/src/Ao.php';

$result = Jsnlib\Export::csv(new Jsnlib\Ao(
[
    'TitleInfo' => ['編號', '姓名', '電話'],
    'ContentList' => 
    [
        0 => ['1', '張先生', '0978-235-235'],
        1 => ['4', '許小姐', '0978-233-111']
    ],
    'return_map' => false,
]));

print_r($result);