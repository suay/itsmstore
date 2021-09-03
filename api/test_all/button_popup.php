<!DOCTYPE html>
<html lang="en">
  <head>
    <script type="text/javascript">
    $(function() {
    $("#myForm").on('submit', function(e) {

        $("#myModal").modal("hide");
        e.preventDefault();

        //submit the form
        $.ajax({
            type: "POST",
            url: '/echo',
            data: $(this).serialize(),
            success: function(data) {
                console.log(data.body.remoteUrl);

                // add content from another url
                $("#myModal2 .modal-body").load(data.body.remoteUrl);

                // open the other modal
                $("#myModal2").modal("show");
            }
        });

    });
});
</script>
  </head>
  <body>
<a href="#myModal" role="button" class="btn btn-primary" data-toggle="modal">Launch modal</a>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Modal 1</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form" role="form" id="myForm">
                    <div class="form-row">
                        <label for="input1" class="col-lg-2 control-label">URL</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="remoteUrl" name="remoteUrl" value="/render/gPp78HigHH">
                        </div>
                    </div>
                    <div class="form-row py-2">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-secondary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Modal 2</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                Some other modal here...
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>