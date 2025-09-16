<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Authority Dashboard</title>
  <style>
    body{font-family:Arial,sans-serif;margin:0;padding:20px;background:#f1f5f9}
    h2{color:#1e40af}
    table{border-collapse:collapse;width:100%;background:#fff}
    th,td{border:1px solid #ddd;padding:8px;text-align:center}
    th{background:#2563eb;color:#fff}
  </style>
</head>
<body>
  <h2>Authority Dashboard</h2>
  <table id="busTable">
    <tr><th>Bus No.</th><th>Latitude</th><th>Longitude</th><th>Last Update</th></tr>
  </table>

  <script>
    function loadData(){
      const data=JSON.parse(localStorage.getItem('busLocations')||"{}");
      const table=document.getElementById('busTable');
      table.innerHTML='<tr><th>Bus No.</th><th>Latitude</th><th>Longitude</th><th>Last Update</th></tr>';
      for(const bus in data){
        const {lat,lng,time}=data[bus];
        const row=`<tr><td>${bus}</td><td>${lat.toFixed(5)}</td><td>${lng.toFixed(5)}</td><td>${new Date(time).toLocaleTimeString()}</td></tr>`;
        table.innerHTML+=row;
      }
    }
    setInterval(loadData,3000);
    loadData();
  </script>
</body>
</html>
