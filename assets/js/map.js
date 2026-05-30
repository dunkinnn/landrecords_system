// map.js
const lotMap = document.getElementById('lotMap');

// Define your lots
const lots = [
  { id: 'LOT 774', points: "300,120 480,160 450,360 320,340", owner: "John Doe", area: "1200 sqm", type: "Residential" },
  { id: 'LOT 763', points: "480,160 560,200 530,350 450,360", owner: "Jane Smith", area: "1000 sqm", type: "Residential" },
  { id: 'LOT 755', points: "560,200 650,240 630,360 530,350", owner: "Mark Lee", area: "1500 sqm", type: "Commercial" },
  // add more lots as needed
];

// Function to show lot info
function showLot(lot) {
  alert(
    "Lot Number: " + lot.id +
    "\nOwner: " + lot.owner +
    "\nArea: " + lot.area +
    "\nLand Type: " + lot.type
  );
}

// Generate polygons dynamically
lots.forEach(lot => {
  const polygon = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
  polygon.setAttribute("points", lot.points);
  polygon.setAttribute("onclick", `showLot(${JSON.stringify(lot)})`);
  
  // Optional: add title for hover tooltip
  const title = document.createElementNS("http://www.w3.org/2000/svg", "title");
  title.textContent = lot.id;
  polygon.appendChild(title);

  lotMap.appendChild(polygon);
});