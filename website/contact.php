<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <?php include 'currency.php'; ?> 
  <?php include 'header.php'; ?>
  <?php include 'navigation.php'; ?>

  <!-- BREADCRUMB -->
  <div id="breadcrumb" class="section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h3 class="breadcrumb-header">Contact Us</h3>
        </div>
      </div>
    </div>
  </div>
  <!-- /BREADCRUMB -->

  <!-- SECTION -->
  <section class="section">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-6">
          <h4>Get in Touch</h4>
          <p><strong>Email:</strong> <a href="mailto:support@bytech.com">support@bytech.com | bytech@email.com</a></p>
          <p><strong>Phone:</strong> <a href="tel:+63286341111">(632) 8634-1111</a></p>
          <p><strong>Address:</strong> De La Salle University, Manila, Philippines</p>
          <p>Our support hours are Monday to Friday, 9:00 AM to 6:00 PM (PHT).</p>
        </div>
        <div class="col-md-6">
          <h4>Send Us a Message</h4>
          <form action="#" method="POST">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Send Message</button>
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h4>Our Location</h4>
          <div class="embed-responsive embed-responsive-16by9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.6014491704354!2d120.99059027457233!3d14.564769377953962!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c97ed286459b%3A0x5927068d997eae2a!2sDe%20La%20Salle%20University%20Manila!5e0!3m2!1sen!2sph!4v1753020423628!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
