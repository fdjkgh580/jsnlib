<?
require_once '../../vendor/autoload.php';

//CSV
$result = Jsnlib\Export::csv(new Jsnlib\Ao(
[
    'TitleInfo' => ['編號', '姓名', '電話'],
    'ContentList' => 
    [
        0 => ['1', '張先生', '0978-235-235'],
        1 => ['4', '許小姐', '0978-233-111']
    ],
    'return_map' => true,
]));

print_r($result);