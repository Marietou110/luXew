"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";

const usersMock = [
  { email: "admin@example.com", password: "admin123", role: "admin" },
  { email: "editor@example.com", password: "editor123", role: "editor" },
];

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const handleLogin = (e) => {
    e.preventDefault();

    const user = usersMock.find(
      (u) => u.email === email && u.password === password
    );

    if (user) {
      // Stocker le rôle dans le localStorage (temporaire)
      localStorage.setItem("userRole", user.role);
      router.push("/dashboard");
    } else {
      setError("Email ou mot de passe incorrect");
    }
  };

  return (
    <div className="container mx-auto p-8 max-w-md">
      <h1 className="text-3xl font-bold text-center text-blue-800 mb-6">
        Connexion
      </h1>
      <form onSubmit={handleLogin} className="bg-white p-6 rounded shadow">
        {error && (
          <p className="text-red-600 mb-4 text-center">{error}</p>
        )}
        <div className="mb-4">
          <label className="block mb-2 font-semibold">Email</label>
          <input
            type="email"
            className="w-full px-4 py-2 border rounded"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="admin@example.com"
            required
          />
        </div>
        <div className="mb-6">
          <label className="block mb-2 font-semibold">Mot de passe</label>
          <input
            type="password"
            className="w-full px-4 py-2 border rounded"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="admin123"
            required
          />
        </div>
        <button
          type="submit"
          className="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
        >
          Se connecter
        </button>
      </form>
    </div>
  );
}
