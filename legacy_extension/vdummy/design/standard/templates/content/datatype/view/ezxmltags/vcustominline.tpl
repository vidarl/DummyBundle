{if and(is_set( $attrLink ), $attrLink )}Title : {$attrLink},{/if}
{if and(is_set( $attrNumber ), $attrNumber )}Number : {$attrNumber},{/if}
{if and(is_set( $attrInt ), $attrInt )}Int : {$attrInt},{/if}
{if and(is_set( $attrCheckbox ), $attrCheckbox )}Checkbox : {$attrCheckbox},{/if}
{if and(is_set( $attrSelect ), $attrSelect )}Select : {$attrSelect},{/if}
{if and(is_set( $attrText ), $attrText )}Text : {$attrText},{/if}
{if and(is_set( $attrTextArea ), $attrTextArea )}TextArea : {$attrTextArea},{/if}

{$content}
