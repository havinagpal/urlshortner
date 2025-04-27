document.addEventListener("DOMContentLoaded", () => {
  const createForm = document.getElementById("create-form")
  const editForm = document.getElementById("edit-form")
  const createResult = document.getElementById("create-result")
  const editResult = document.getElementById("edit-result")

  createForm.addEventListener("submit", (e) => {
    e.preventDefault()
    const longUrl = document.getElementById("long-url").value
    const customPath = document.getElementById("custom-path").value

    // Show loading state
    createResult.innerHTML = '<p class="loading">Creating short URL...</p>'
    createResult.style.opacity = "1"
    createResult.style.maxHeight = "200px"

    // Send request to the backend
    fetch("api/create.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        longUrl: longUrl,
        customPath: customPath,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          createResult.innerHTML = `<p class="error">Error: ${data.error}</p>`
        } else {
          const shortUrl = data.shortUrl
          createResult.innerHTML = `
                    <p class="success-message">Short URL created successfully!</p>
                    <div style="display: flex; align-items: center; margin-top: 10px;">
                        <a href="${shortUrl}" target="_blank">${shortUrl}</a>
                        <button class="copy-btn tooltip" data-tooltip="Copy to clipboard" onclick="copyToClipboard('${shortUrl}', this)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div id="qr-code" style="margin-top: 15px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(shortUrl)}" alt="QR Code" />
                    </div>
                `
        }
      })
      .catch((error) => {
        createResult.innerHTML = `<p class="error">Error: ${error.message}</p>`
      })

    createForm.reset()
  })

  editForm.addEventListener("submit", (e) => {
    e.preventDefault()
    const shortUrl = document.getElementById("edit-short-url").value
    const newLongUrl = document.getElementById("edit-long-url").value

    // Show loading state
    editResult.innerHTML = '<p class="loading">Updating link...</p>'
    editResult.style.opacity = "1"
    editResult.style.maxHeight = "200px"

    // Send request to the backend
    fetch("api/update.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        shortUrl: shortUrl,
        newLongUrl: newLongUrl,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          editResult.innerHTML = `<p class="error">Error: ${data.error}</p>`
        } else {
          editResult.innerHTML = `<p class="success-message">Link updated successfully!</p>`
        }
      })
      .catch((error) => {
        editResult.innerHTML = `<p class="error">Error: ${error.message}</p>`
      })

    editForm.reset()
  })
})

// Function to copy text to clipboard
function copyToClipboard(text, button) {
  navigator.clipboard.writeText(text).then(
    () => {
      button.classList.add("copied")

      // Change tooltip text temporarily
      const originalTooltip = button.getAttribute("data-tooltip")
      button.setAttribute("data-tooltip", "Copied!")

      // Reset after animation completes
      setTimeout(() => {
        button.classList.remove("copied")
        button.setAttribute("data-tooltip", originalTooltip)
      }, 2000)
    },
    (err) => {
      console.error("Could not copy text: ", err)
    },
  )
}

