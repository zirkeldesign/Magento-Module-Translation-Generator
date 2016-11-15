<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Magento Module Translation Generator</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <h1>Translation Generator</h1>
      <form id="input_form" action="IndexController.php" method="GET">
        <div class="form-group">
          <label for="module_path">Module Path</label>
          <textarea class="form-control" rows="4" name="path" id="module_path">sample_extension</textarea>
        </div>
        <div class="form-group">
          <label for="module_language">Language</label>
          <select class="form-control" name="lang" id="module_language">
            <option value="en">EN</option>
            <option value="de">DE</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
    <div class="container">
      <h2>Generator Output</h2>
      <div id="output"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.3.1/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
    <script>
(function(window, $) {
  var $output = $('#output')
  $('#input_form').on('submit', function(event) {
    event.preventDefault();
    var $form = $(this);
    $output.html('');
    $.ajax({
      type: 'POST',
      url: $form.attr('action'),
      data: $form.serialize(),
    })
    .done(function(data) {
      $output.html(data);
    });
  });
})(window, jQuery);
</script>
  </body>
</html>