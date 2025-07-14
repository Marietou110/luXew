"use client";
import { useEffect, useState } from "react";
import Link from "next/link";

export default function Categories() {
  const [categories, setCategories] = useState([
    { id: 1, name: "Politique" },
    { id: 2, name: "Sport" },
    { id: 3, name: "Technologie" },
    { id: 4, name: "Culture" },
  ]);

  const [newCategory, setNewCategory] = useState("");
  const [userRole, setUserRole] = useState(null);

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    setUserRole(role);
  }, []);

  const handleAddCategory = () => {
    if (newCategory.trim() === "") return;
    const nextId = categories.length ? categories[categories.length - 1].id + 1 : 1;
    setCategories([...categories, { id: nextId, name: newCategory }]);
    setNewCategory("");
  };

  const handleDeleteCategory = (id) => {
    if (confirm("Voulez-vous vraiment supprimer cette catégorie ?")) {
      setCategories(categories.filter((cat) => cat.id !== id));
    }
  };

  return (
    <div className="max-w-4xl mx-auto p-8">
      <h1 className="text-4xl font-bold mb-6 text-indigo-900">Catégories</h1>

      {userRole && (userRole === "admin" || userRole === "editor") && (
        <div className="mb-6 flex space-x-4">
          <input
            type="text"
            placeholder="Nouvelle catégorie"
            value={newCategory}
            onChange={(e) => setNewCategory(e.target.value)}
            className="flex-grow px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <button
            onClick={handleAddCategory}
            className="bg-indigo-600 text-white px-6 rounded hover:bg-indigo-700 transition"
          >
            Ajouter
          </button>
        </div>
      )}

      <ul className="space-y-4">
        {categories.map(({ id, name }) => (
          <li
            key={id}
            className="flex justify-between items-center bg-indigo-50 p-4 rounded shadow hover:bg-indigo-100 transition"
          >
            <Link href={`/categories/${id}`} className="text-indigo-900 font-semibold hover:underline">
              {name}
            </Link>

            {(userRole === "admin" || userRole === "editor") && (
              <button
                onClick={() => handleDeleteCategory(id)}
                className="text-red-600 hover:text-red-800 font-bold"
              >
                Supprimer
              </button>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
}
