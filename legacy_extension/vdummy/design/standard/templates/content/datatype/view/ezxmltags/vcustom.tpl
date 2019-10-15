<div>
    {if and(is_set( $attrTitle ), $attrTitle )}Title : {$attrTitle}<br/>{/if}
    {if and(is_set( $attrDesc ), $attrDesc )}Description : {$attrDesc}<br/>{/if}
    color : {$attrColor}<br/>
    node_id:{$#node.node_id}<br/>
    contentobject_id:{$#node.contentobject_id}<br/>
    {$content}
</div>
