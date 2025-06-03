<!DOCTYPE html>
<html lang="<?php echo $settings['lang']; ?>">

<head>
  <title>Administration | <?php echo $settings['title']; ?></title>
  <link rel="stylesheet" href="templates/default/gallery.css">
  <style type="text/css">
    #Loader {
      display: none;
      position: absolute;
      top: 140px;
      left: 20%;
      width: 50%;
      height: 50%;
      font-size: 3em;
      text-align: center;
      background: rgba(243, 243, 250, 0.9);
      border-radius: 15px;
    }

    #Loader .text {
      position: relative;
      top: 36%;
    }
  </style>
</head>

<body>
  <header id="site_header">
    <h1><?php echo $settings['title']; ?></h1>
    <nav>
      <ol>
        <nav><?php echo $HTML_navigation; ?></nav>
      </ol>
    </nav>
    <div class="clear"></div>
  </header>
  <div id="messageArea">
  <?php echo $action_status_message; ?>
  </div>
  <article>
    <div id="main">
    <?php echo $HTML_article_content; ?>
    </div>
  </article>
  <footer>
    <nav>
      <ol>
        <li><a href="admin.php">Administration</a></li>
      </ol>
    </nav>
  </footer>
  <div id="Loader">
    <div class="text">
      <p>Uploader...</p>
      <div>&#x279f;&#x279f;</div>
    </div>
  </div>
  <script>
    async function uploadFile() {
      const fileInput = document.getElementById('fileToUpload');
      const categorySelect = document.querySelector('select[name="category"]');
      const loader = document.getElementById('Loader');
      const file = fileInput.files[0];
      const category = categorySelect.value;

      if (!file) return alert("No file selected.");
      if (loader) loader.style.display = "block";

      const chunkSize = 512 * 1024;
      const totalChunks = Math.ceil(file.size / chunkSize);

      for (let i = 0; i < totalChunks; i++) {
        const chunk = file.slice(i * chunkSize, (i + 1) * chunkSize);
        const formData = new FormData();
        formData.append('chunkIndex', i);
        formData.append('totalChunks', totalChunks);
        formData.append('filename', file.name);
        formData.append('category', category);
        formData.append('chunk', chunk);

        try {
          const res = await fetch('upload.php', {
            method: 'POST',
            body: formData
          });
          const data = await res.json();
          showStatusMessage(data.message);
        } catch (err) {
          showStatusMessage('Error');
          console.error("Error uploading chunk", i, err);
          if (loader) loader.style.display = "none";
          return;
        }
      }

      if (loader) loader.style.display = "none";
      document.getElementById('fileToUpload').value = '';

      function showStatusMessage(msg) {
        let statusMessage = document.getElementById('action_status_message');
        if (!statusMessage) {
          const messageArea = document.getElementById('messageArea');
          container = document.createElement('div');
          container.id = 'action_status_message';
          messageArea.appendChild(container);
        }
        container.innerHTML = msg;
      }
    }
  </script>

</body>

</html>