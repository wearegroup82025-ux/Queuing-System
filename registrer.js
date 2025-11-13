/* registrar.js
   - sidebar nav working
   - queue -> approve -> approved flow
   - dashboard counters update
*/

// --------- Sidebar navigation ----------
const sidebarItems = document.querySelectorAll(".sidebar-item");
const sections = document.querySelectorAll(".section");

sidebarItems.forEach(item => {
  item.addEventListener("click", () => {
    // set active class on sidebar
    sidebarItems.forEach(i => i.classList.remove("active"));
    item.classList.add("active");

    // show target section
    const target = item.getAttribute("data-target");
    sections.forEach(s => s.classList.remove("active"));
    const el = document.getElementById(target);
    if (el) el.classList.add("active");
  });
});

// --------- Sample data ----------
let queue = [
  { name: "Maria Santos", purpose: "Enrollment Verification", status: "Pending", createdAt: new Date() },
  { name: "Juan Dela Cruz", purpose: "Document Request", status: "Pending", createdAt: new Date() },
  { name: "Ana Reyes", purpose: "Form Submission", status: "Approved", createdAt: new Date() }
];

let approved = []; // will hold approved records (copied from queue when approved)

// Pre-populate approved from queue items that are already Approved
queue.forEach((q, idx) => {
  if (q.status === "Approved") {
    approved.push({
      queueNo: approved.length + 1,
      name: q.name,
      purpose: q.purpose,
      dateApproved: new Date()
    });
  }
});

// --------- Rendering functions ----------
function renderQueueTable() {
  const tbody = document.querySelector("#queueTable tbody");
  tbody.innerHTML = "";
  const pending = queue.filter(q => q.status === "Pending");
  pending.forEach((q, i) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${escapeHtml(q.name)}</td>
      <td>${escapeHtml(q.purpose)}</td>
      <td>${q.status}</td>
      <td><button class="approve-btn" data-index="${getGlobalQueueIndex(q)}">Approve</button></td>
    `;
    tbody.appendChild(tr);
  });
  attachApproveListeners();
}

function renderApprovedTable() {
  const tbody = document.querySelector("#approvedTable tbody");
  tbody.innerHTML = "";
  approved.forEach((a, i) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${escapeHtml(a.name)}</td>
      <td>${escapeHtml(a.purpose)}</td>
      <td>${formatDate(a.dateApproved)}</td>
    `;
    tbody.appendChild(tr);
  });
}

function updateDashboard() {
  const pendingCount = queue.filter(q => q.status === "Pending").length;
  const approvedCount = approved.length;
  const totalProcessed = queue.length;

  document.getElementById("pendingCount").textContent = pendingCount;
  document.getElementById("approvedCount").textContent = approvedCount;
  document.getElementById("totalProcessed").textContent = totalProcessed;
}

// --------- Utilities ----------
function formatDate(d) {
  const dt = new Date(d);
  return dt.toLocaleDateString();
}
function escapeHtml(text){
  const el = document.createElement('div');
  el.textContent = text;
  return el.innerHTML;
}
// Because 'queue' array may contain both pending and approved, find the index of an object instance
function getGlobalQueueIndex(item) {
  return queue.indexOf(item);
}

// --------- Approve flow ----------
function attachApproveListeners() {
  const btns = document.querySelectorAll(".approve-btn");
  btns.forEach(btn => {
    btn.removeEventListener("click", approveClickHandler); // safe remove
    btn.addEventListener("click", approveClickHandler);
  });
}

function approveClickHandler(e) {
  const idx = parseInt(e.currentTarget.getAttribute("data-index"), 10);
  if (!Number.isInteger(idx) || idx < 0) return alert("Invalid index");

  // mark as approved and move to approved array
  queue[idx].status = "Approved";
  approved.push({
    queueNo: approved.length + 1,
    name: queue[idx].name,
    purpose: queue[idx].purpose,
    dateApproved: new Date()
  });

  renderAll();
}

// --------- Add sample / refresh buttons ----------
document.getElementById("btnAddSample").addEventListener("click", () => {
  const name = prompt("Student name for sample:");
  if (!name) return;
  const purpose = prompt("Purpose (e.g. Enrollment):", "Enrollment Verification") || "Enrollment Verification";
  queue.push({ name, purpose, status: "Pending", createdAt: new Date() });
  renderAll();
});
document.getElementById("btnRefresh").addEventListener("click", renderAll);

// --------- Render all UI ----------
function renderAll() {
  renderQueueTable();
  renderApprovedTable();
  updateDashboard();
}

// Initial render
renderAll();

// Expose approve for older onclick patterns if needed (not required here)
window.approve = function(index){
  if (index == null) return;
  if (!queue[index]) return;
  queue[index].status = "Approved";
  approved.push({
    queueNo: approved.length + 1,
    name: queue[index].name,
    purpose: queue[index].purpose,
    dateApproved: new Date()
  });
  renderAll();
};
