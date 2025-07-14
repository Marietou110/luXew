"use client";
import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";

export default function Dashboard() {
  const router = useRouter();
  const [role, setRole] = useState("");

useEffect(() => {
  const userRole = localStorage.getItem("userRole");

  if (!userRole) {
    console.log("Aucun rôle trouvé, redirection vers login");
    setTimeout(() => {
      router.push("/login");
    }, 1000); // petite pause d'1 seconde
  } else {
    console.log("Rôle trouvé :", userRole);
    setRole(userRole);
  }
}, [router]);

  return (
    <div className="container mx-auto p-8">
      <h1 className="text-4xl font-bold text-blue-800 mb-6">
        Bienvenue sur le Dashboard
      </h1>
      <p className="text-lg text-gray-700 mb-4">
        Vous êtes connecté en tant que : <span className="font-bold">{role}</span>
      </p>

      <div className="grid grid-cols-1 gap-4 mt-8">
        <Link
          href="/articles"
          className="block p-4 bg-green-500 text-white rounded text-center hover:bg-green-600"
        >
          📄 Gérer les Articles
        </Link>
        <Link
          href="/categories"
          className="block p-4 bg-blue-500 text-white rounded text-center hover:bg-blue-600"
        >
          📂 Gérer les Catégories
        </Link>
        {role === "admin" && (
          <Link
            href="/users"
            className="block p-4 bg-red-500 text-white rounded text-center hover:bg-red-600"
          >
            👥 Gérer les Utilisateurs
          </Link>
        )}
      </div>
    </div>
  );
}
