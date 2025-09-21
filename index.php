<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SmartBus Tracker - Improved Card Hover</title>
  <style>
    :root{
      --bg1: #ebf8ff;
      --bg2: #dbeafe;
      --card-bg: #ffffff;
      --primary: #2563eb;
      --success: #16a34a;
      --muted: #6b7280;
    }

    *{box-sizing: border-box}

    body{
      margin:0;
      font-family: Inter, Arial, sans-serif;
      background: linear-gradient(135deg, var(--bg1), var(--bg2));
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:30px;
    }

    .container{
      width:100%;
      max-width:1000px;
      text-align:center;
    }

    h1{
      margin:0 0 8px;
      color:#1e40af;
      font-size:2.25rem;
    }

    p.subtitle{
      color: #374151;
      margin-bottom:20px;
    }

    /* grid */
    .grid{
      display:grid;
      grid-template-columns:1fr;
      gap:28px;
    }

    @media(min-width:768px){
      .grid{ grid-template-columns:1fr 1fr; }
    }

    /* card base */
    .card{
      position:relative;
      background:var(--card-bg);
      border-radius:16px;
      padding:30px 26px;
      overflow:hidden;
      cursor:pointer;
      transform-style:preserve-3d;
      transition: transform 350ms cubic-bezier(.2,.9,.3,1), 
                  box-shadow 350ms cubic-bezier(.2,.9,.3,1);
      will-change: transform, box-shadow;
      box-shadow: 0 6px 18px rgba(16,24,40,0.06);
      min-height:180px;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
    }

    /* subtle icon */
    .card .icon{
      font-size:3.2rem;
      margin-bottom:12px;
      transition: transform 350ms;
      transform-origin:center;
    }

    .card h2{
      margin:0;
      font-size:1.4rem;
      color:#0f172a;
    }

    .card p{
      color:var(--muted);
      margin:12px 0 18px;
      font-size:0.95rem;
    }

    /* hover overlay (gradient) */
    .card::after{
      content:"";
      position:absolute;
      inset:0;
      background:linear-gradient(120deg, rgba(37,99,235,0.06), rgba(6,95,70,0.03));
      opacity:0;
      transform:translateY(8px);
      transition: opacity 300ms, transform 300ms;
      pointer-events:none;
    }

    /* action button hidden initially */
    .action-btn{
      display:inline-block;
      padding:10px 18px;
      border-radius:10px;
      border:none;
      color:white;
      font-weight:600;
      letter-spacing:0.2px;
      transform: translateY(12px);
      opacity:0;
      transition: transform 320ms cubic-bezier(.2,.9,.3,1), opacity 320ms;
      box-shadow: 0 8px 20px rgba(16,24,40,0.08);
    }

    /* different color variants */
    .btn-blue{ background:var(--primary); }
    .btn-green{ background:var(--success); }

    /* improved hover state */
    .card:hover, .card.card--focus {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 18px 40px rgba(14,30,60,0.12);
    }

    .card:hover .icon, .card.card--focus .icon{
      transform: rotate(-6deg) scale(1.04);
    }

    .card:hover::after, .card.card--focus::after{
      opacity:1;
      transform:translateY(0);
    }

    .card:hover .action-btn, .card.card--focus .action-btn{
      transform: translateY(0);
      opacity:1;
    }

    /* small decorative accent */
    .card .accent {
      position:absolute;
      right:-40px;
      top:-40px;
      width:140px;
      height:140px;
      background: radial-gradient(circle at 30% 30%, rgba(37,99,235,0.12), transparent 40%);
      transform: rotate(25deg);
      filter: blur(8px);
      pointer-events:none;
      transition: opacity 300ms;
      opacity:0;
    }

    .card:hover .accent, .card.card--focus .accent{ opacity:1; }

    /* focus styles for keyboard accessibility */
    .card:focus-within{ outline: none; }

    .fake-link{
      display:inline-block;
      margin-top:16px;
      font-size:0.9rem;
      color:#374151;
      text-decoration:underline;
    }

    /* small helper: accessible focus ring on click/tab */
    .card:focus-visible{
      box-shadow: 0 18px 40px rgba(14,30,60,0.14), 
                  0 0 0 4px rgba(37,99,235,0.12);
      transform: translateY(-10px) scale(1.02);
    }

    /* responsive tweaks */
    @media (max-width:480px){
      .card{ padding:22px; min-height:150px; }
      .card .icon{ font-size:2.6rem; }
      .action-btn{ padding:9px 14px; }
    }

    .authority-btn {
      position: relative;
      padding: 12px 28px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      background: linear-gradient(90deg, #6366f1, #3b82f6, #2563eb);
      background-size: 200% 200%;
      animation: gradientShift 4s ease infinite;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(37,99,235,0.35);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    /* hover scaling */
    .authority-btn:hover {
      transform: scale(1.08);
      box-shadow: 0 10px 25px rgba(37,99,235,0.45);
    }

    /* gradient movement */
    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* shiny reflection line */
    .authority-btn::after {
      content: "";
      position: absolute;
      top: 0;
      left: -75px;
      width: 50px;
      height: 100%;
      background: rgba(255,255,255,0.6);
      transform: skewX(-20deg);
      opacity: 0;
    }

    .authority-btn:hover::after {
      animation: shine 1s forwards;
    }

    @keyframes shine {
      0% { left: -75px; opacity: 0; }
      30% { opacity: 1; }
      100% { left: 120%; opacity: 0; }
    }
  </style>
</head>
<body>


<!-------------------------------------------------------------------------------------------->
    <script>
      function goToLogin(role) {
      // Redirect to login.php and pass role in URL
      window.location.href = "login.php?role=" + encodeURIComponent(role);;
    }
    </script>
<!-------------------------------------------------------------------------------------------->


  <!--STARTS HERE-->

  <div class="container" role="main">
    <h1>SmartBus Tracker</h1>
    <p class="subtitle">Track buses in real-time — choose who you are to continue</p>

    <div class="grid" id="grid">
      <div class="card" tabindex="0" data-role="passenger">
        <div class="accent"></div>
        <div class="icon" aria-hidden>🚍</div>
        <h2>I'm a Passenger</h2>
        <p>Find nearby buses, check live location and ETA for stops.</p>
        <button class="action-btn btn-blue" onclick="goToLogin('user')">Continue</button>
      </div>

      <div class="card" tabindex="0" data-role="driver">
        <div class="accent"></div>
        <div class="icon" aria-hidden>👨‍✈️</div>
        <h2>I'm a Driver</h2>
        <p>Start/stop trips and share live location with passengers.</p>
        <button class="action-btn btn-green" onclick="goToLogin('driver')">Continue</button>
      </div>
    </div>

    <div style="margin-top:26px;">
      <div style="margin-top:40px;">
        <button class="authority-btn" onclick="goToLogin('authority')">
          Authority Login
        </button>
      </div>
    </div>
  </div>

  <!--ENDS HERE-->

</body>
</html>
