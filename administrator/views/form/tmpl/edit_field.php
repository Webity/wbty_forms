<?php  
defined('_JEXEC') or die;
?>
<tr id=\'id_'. $row['id']. '\'> <td class=\'span3\'> <i class=\'icon-resize-vertical\'> </i> ' . $row['ex_name'] .'</td>
<td>Max Weight:</td>
<td><input value="'.$this->maxValues['maxweight'][$row['exercise_id']] . '" class="wt_max exercise'.$row['exercise_id'].'" name="jform[wtmax]['.$row['exercise_id'].']" type="text" size="5"> </input> </td>
<td>Max Reps:</td>
<td><input value="'.$this->maxValues['maxrep'][$row['exercise_id']] . '" class="rep_max exercise'.$row['exercise_id'].'" name="jform[repmax]['.$row['exercise_id'].']" type="text" size="5"> </input> </td> <td><span id=\'recalc_cell'. $row['ordering']. '\' class="calc_max"> <a class=\'btn-mini btn-info \' > Recalculate </a> </span></td><td>One Rep Max:</td><td><input value="'.$this->maxValues['onerepmax'][$row['exercise_id']] . '" class="one_rep_max exercise'.$row['exercise_id'].'" name="jform[onerepmax]['.$row['exercise_id'].']" type="text" size="5"> </input> </td>
<td> <span> <a href="#formulaModal" class=\'btn-mini btn-info formula_override\' data-toggle="modal"> Specify Formula </a> <input type="text" name="jform[formula_override][]" value="'. $row['formula_override']. '" class="formula_override" readonly="readonly" size="5" /> </span> </td>
<td> <span> <a class=\'btn-mini btn-info duplicate_row\'> Duplicate </a>  </span> </td>
<td> <span id=\'delete_cell'. $row['ordering']. '\' class=
"delete_exercise"> <a class=\'btn-mini btn-danger \' > X </a> </span> <input type="hidden" class=\'exercises\' name="jform[routine][]" value="'. $row['exercise_id']. '"></td> </tr>
