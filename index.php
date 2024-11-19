
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DISH-COVERY</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="mediaqueries.css" />
</head>

<body>
<form action="signup.php" method="post"></form>
  <nav id="desktop-nav">
    <div class="logo">Dish-covery</div>
    <div>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
  </nav>

  <nav id="hamburger-nav">
    <div class="logo">Dish-covery</div>
    <div class="hamburger-menu" onclick="toggleMenu()">
      <div class="hamburger-icon">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <div class="menu-links">
        <ul>
          <li><a href="#home" onclick="toggleMenu()">Home</a></li>
          <li><a href="#about" onclick="toggleMenu()">About Us</a></li>
          <li><a href="#features" onclick="toggleMenu()">Features</a></li>
          <li><a href="#contact" onclick="toggleMenu()">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>


  <section id="home">
    <div class="section__text">
      <h1 class="title">DISH-COVERY: A social platform for recipe sharing and exploration</h1>
      <p class="section__text__p2">Welcome to Dish-covery! Whether you’re an aspiring chef, a seasoned home cook, or a passionate food lover, Dish-covery is here to inspire and elevate your culinary journey.</p>
      <div class="btn-container">
        <button class="btn btn-color-2" onclick="window.location.href='login.php'"> Login</button>

        <button class="btn btn-color-2" onclick="window.location.href='signup.php'">Sign up</button>

      </div>
      <div id="socials-container"></div>
    </div>
  </section>

  <section id="about">
    <h1 class="title">About Us</h1>
      <div class="section__pic-container">
        <p>We believe that food is not just sustenance; it's an experience that brings people together. Our mission is to connect food lovers with delicious recipes, local dining experiences, and culinary inspiration from around the globe.</p>
      </div>
  </section>

  <section id="features">
    <h1 class="title">Features</h1>
    <div class="title-align">
      <div class="feature-item">
        <h4 class="title">Post recipe</h4>
        <p>Share your culinary creations with the Dishcovery community! Our "Post Recipe" feature allows you to showcase your favorite dishes, whether they're family legacies or innovative new experiments.</p>
      </div>
      <div class="feature-item">
        <h3 class="title">Nutrition Tracker</h3>
        <p>Take charge of your health with our comprehensive Nutrition Tracker! Designed to help you monitor your dietary intake, this feature empowers you to make informed choices about what you eat.</p>
      </div>
      <div class="feature-item">
        <h2 class="title">Meal Planning</h2>
        <p>Simplify your culinary journey with our intuitive Meal Planning feature! Designed to help you organize your meals efficiently, this tool makes it easy to create balanced and delicious menus for any week.</p>
      </div>
    </div>
  </section>


  <section id="contact">
    <h1 class="title">Contact Us</h1>

    <div class="contact-container">
      <!-- Contact Information -->
      <div class="contact-info">
        <h2>Get in Touch</h2>
        <p><strong>Email:</strong> <a href="dish-covery@gmail.com">dish-covery@gmail.com</a></p>
        <p><strong>Phone:</strong> 09668001607</p>
        <p><strong>Address:</strong> Lipa City, Batangas</p>
      </div>

      <!-- Contact Form -->
      <div class="contact-form">
        <h2>Send Us a Message</h2>
        <form action="#" method="post">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" required placeholder="Your Name">

          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required placeholder="Your Email">

          <label for="message">Message:</label>
          <textarea id="message" name="message" rows="5" required placeholder="Your Message"></textarea>

          <button type="submit">Send Message</button>
        </form>
      </div>
    </div>
  </section>


  <footer>
    <nav>
      <div class="nav-links-container">
        <ul class="nav-links">
          <li><a href="#home">Home</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
    </nav>
    <p>Copyright &#169; Dish-covery. All Rights Reserved.</p>
  </footer>

  <script src="script.js"></script>
</body>

</html>