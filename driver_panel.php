<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Driver Panel</title>
  <style>
    body{
      font-family:Arial,sans-serif;
      margin:0;
      padding:20px;
      background:#f8fafc
    }
    .card{
      background:#fff;
      padding:20px;
      border-radius:12px;
      box-shadow:0 6px 18px rgba(0,0,0,0.06)
    }
    input{
      padding:10px;
      border-radius:8px;
      border:1px solid #ddd;
      width:100%;
      margin-bottom:10px
    }
    button{
      padding:10px 14px;
      border:none;
      border-radius:8px;
      margin-right:10px;
      cursor:pointer;
      font-weight:600;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .start{
      background:#16a34a;
      color:#fff;
    }
    .stop{
      background:#ef4444;
      color:#fff;
    }

    /* Hover effects */
    .start:hover{
      background:#15803d;
      transform: scale(1.05);
      box-shadow:0 4px 12px rgba(22,163,74,0.4);
    }
    .stop:hover{
      background:#dc2626;
      transform: scale(1.05);
      box-shadow:0 4px 12px rgba(239,68,68,0.4);
    }
  </style>
</head>
<body>
  <h2>Driver Panel</h2>
  <div class="card">
    <input id="busNo" type="text" placeholder="Enter your Bus/Van Number">
    <button class="start" onclick="startTrip()">Start Trip</button>
    <button class="stop" onclick="endTrip()">End Trip</button>
    <div id="status">Status: Idle</div>
  </div>

  <script>
    let interval;
    let lat = 26.8467, lng = 80.9462; // Lucknow coords as starting point

    function startTrip(){
      const bus = document.getElementById('busNo').value.trim();
      if(!bus){ alert("Enter bus number"); return; }
      if(interval){ alert("Trip already started"); return; }

      document.getElementById('status').textContent=`Status: Trip started for ${bus}`;

      interval = setInterval(()=>{
        lat += (Math.random()-0.5)*0.002;
        lng += (Math.random()-0.5)*0.002;

        // save each bus data in "bus_<number>" format
        localStorage.setItem("bus_" + bus, JSON.stringify({
          lat, lng, time: new Date().toLocaleTimeString()
        }));
      }, 3000);
    }

    function endTrip(){
      clearInterval(interval); 
      interval = null;
      document.getElementById('status').textContent="Status: Trip ended";
    }
  </script>
</body>
</html>
