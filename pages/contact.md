---
layout: default
title: Contact
permalink: /contact/
---

<div style="height:64px"></div>

<main class="container py-5">
  <h1 class="section-title mb-4">Contact</h1>
  <div class="row g-4">
    <div class="col-12 col-lg-7">
      <div class="card card-modern p-4">
        <h2 class="h4 fw-bold">Send a Message</h2>
        <form id="contactForm" action="contact.php" method="POST" novalidate>
          <p style="display:none;"><label>Do not fill: <input name="bot-field"></label></p>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label" for="name">Name</label>
              <input class="form-control" id="name" name="name" required>
              <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="email">Email</label>
              <input class="form-control" type="email" id="email" name="email" required>
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="col-12">
              <label class="form-label" for="message">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
              <div class="invalid-feedback">Please enter a message.</div>
            </div>
          </div>
          <div class="d-grid d-sm-flex gap-2 mt-3">
            <button class="btn btn-noor" type="submit"><i class="bi bi-send me-1"></i> Send</button>
            <button class="btn btn-outline-secondary" type="reset">Reset</button>
          </div>
        </form>
        <div id="successMessage" class="alert alert-success mt-3 d-none" role="alert">
          <h4 class="alert-heading">Thank you!</h4>
          <p>Your message has been sent. We’ll get back to you within one business day.</p>
        </div>
        <div id="errorMessage" class="alert alert-danger mt-3 d-none" role="alert">
          <h4 class="alert-heading">Oops!</h4>
          <p>Something went wrong. Please try again or email <a href="mailto:info@noortutoring.com">info@noortutoring.com</a>.</p>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-5">
      <div class="p-4 rounded-2xl hero-card">
        <h3 class="h5 fw-bold mb-3">Contact Info</h3>
        <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:info@noortutoring.com">info@noortutoring.com</a></p>
        <!--<p class="mb-0"><i class="bi bi-telephone me-2"></i><a href="tel:+1-000-000-0000">+1 (000) 000-0000</a></p>-->
      </div>
    </div>
  </div>
</main>

<script>
(() => {
  const form = document.getElementById('contactForm');
  const success = document.getElementById('successMessage');
  const errorBox = document.getElementById('errorMessage');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorBox.classList.add('d-none');
    if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
    form.classList.remove('was-validated');
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
      const res = await fetch(form.action, { method:'POST', headers:{'Accept':'application/json'}, body:new FormData(form) });
      if (res.ok) { form.classList.add('d-none'); success.classList.remove('d-none'); success.scrollIntoView({behavior:'smooth'}); }
      else { errorBox.classList.remove('d-none'); }
    } catch(err) { errorBox.classList.remove('d-none'); }
    finally { btn.disabled = false; }
  });
})();
</script>