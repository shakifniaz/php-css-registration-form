async function loadUsers() {
    const res = await fetch("register.php?action=get");
    const users = await res.json();
    const list = document.getElementById("user-list");
    list.innerHTML = "";

    if (users.length === 0) {
        list.innerHTML = `<p class="text-center text-gray-500">No users registered yet.</p>`;
        return;
    }

    users.forEach((user) => {
        const div = document.createElement("div");
        div.className = "flex justify-between items-center p-4 border rounded bg-[#ecdbff] hover:bg-[#d6bfff]";
        div.innerHTML = `
      <div>
        <p class="font-semibold">${user.first_name} ${user.last_name}</p>
        <p class="text-sm text-gray-500">${user.email}</p>
        <p class="text-sm text-gray-400">${user.dob}</p>
        <p class="text-sm text-gray-400">${user.address || ''}</p>
      </div>
      <div class="space-x-2">
        <button onclick="openEdit(${user.id}, '${user.first_name}', '${user.last_name}', '${user.email}', '${user.dob}', '${user.address || ''}')"
          class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
        <button onclick="deleteUser(${user.id})"
          class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
      </div>`;
        list.appendChild(div);
    });
}

async function deleteUser(id) {
    if (!confirm("Are you sure you want to delete this user?")) return;
    await fetch(`register.php?action=delete&id=${id}`);
    loadUsers();
}

function openEdit(id, first, last, email, dob, address) {
    document.getElementById("editModal").classList.remove("hidden");
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_first_name").value = first;
    document.getElementById("edit_last_name").value = last;
    document.getElementById("edit_email").value = email;
    document.getElementById("edit_dob").value = dob;
    document.getElementById("edit_address").value = address;
}

document.getElementById("cancelEdit").addEventListener("click", () => {
    document.getElementById("editModal").classList.add("hidden");
});

document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
        id: document.getElementById("edit_id").value,
        first_name: document.getElementById("edit_first_name").value,
        last_name: document.getElementById("edit_last_name").value,
        email: document.getElementById("edit_email").value,
        dob: document.getElementById("edit_dob").value,
        address: document.getElementById("edit_address").value,
    };
    await fetch("register.php?action=edit", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    });
    document.getElementById("editModal").classList.add("hidden");
    loadUsers();
});

loadUsers();
