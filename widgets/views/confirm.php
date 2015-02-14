<!-- add Tooltip to link -->
<?php

// replace by default the modal content by the new loaded content
$confirm = 'function(html){ $("#confirmModal_' . $uniqueID . '").html(html);}';


?>

    <!-- create button element -->
    <button class="" data-toggle="modal"
            data-target="#confirmModal_<?php echo $uniqueID; ?>" >
        <?php echo $linkContent; ?>
    </button>



<!-- start: Confirm modal -->
<div class="modal" id="confirmModal_<?php echo $uniqueID; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-extra-small animated pulse">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body text-center">
                <?php echo $message; ?>
            </div>
            <div class="modal-footer">
                <?php if ($buttonTrue != "") { ?>
                   <a class="btn btn-primary" href="<?php echo $linkHref; ?>"><?php echo $buttonTrue; ?></a>
                <?php } ?>
                <?php if ($buttonFalse != "") { ?>
                    <button type="button" class="btn btn-primary"
                            data-dismiss="modal"><?php echo $buttonFalse; ?></button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        // move modal to body
        $('#confirmModal_<?php echo $uniqueID; ?>').appendTo(document.body);



    });


    $('#confirmModal_<?php echo $uniqueID; ?>').on('shown.bs.modal', function (e) {

        // remove standard modal with
        $('#confirmModal_<?php echo $uniqueID; ?> .modal-dialog').attr('style', '');
    });


</script>