<?php  
defined('_JEXEC') or die;
//var_dump($this->item);
?>
<tr id="field_<?php echo $this->item->id; ?>">
<td><?php echo $this->item->options['label']['value']; ?></td>
<td><?php echo JText::_($this->item->field_type); ?></td>
<td><?php echo ($this->item->options['required']['value'] ? "Yes" : "No"); ?></td>
<td> <input type="hidden" name="jfields[]" value="<?php echo $this->item->id; ?>" /><span class="edit_field"> <a class='button btn btn-info' > Edit </a> </span> <span class="delete_field"> <a class='button btn btn-danger' > Remove </a> </span></td>
</tr>
