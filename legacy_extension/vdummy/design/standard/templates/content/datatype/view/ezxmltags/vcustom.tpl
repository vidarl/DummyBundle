<div>
    {if and(is_set( $attrLink ), $attrLink )}Title : {$attrLink}<br/>{/if}
    {if and(is_set( $attrNumber ), $attrNumber )}Number : {$attrNumber}<br/>{/if}
    {if and(is_set( $attrInt ), $attrInt )}Int : {$attrInt}<br/>{/if}
    {if and(is_set( $attrCheckbox ), $attrCheckbox )}Checkbox : {$attrCheckbox}<br/>{/if}
    {if and(is_set( $attrSelect ), $attrSelect )}Select : {$attrSelect}<br/>{/if}
    {if and(is_set( $attrText ), $attrText )}Text : {$attrText}<br/>{/if}
    {if and(is_set( $attrTextArea ), $attrTextArea )}TextArea : {$attrTextArea}<br/>{/if}
    node_id:{$#node.node_id}<br/>
    contentobject_id:{$#node.contentobject_id}<br/>
    {$content}
</div>
