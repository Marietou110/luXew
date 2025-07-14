"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";

export default function Signup() {
  const router = useRouter();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirm, setPasswordConfirm] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  const handleSignup = (e) => {
    e.preventDefault();
    setError("");

    if (!email || !password || !passwordConfirm) {
      setError("Merci de remplir tous les champs.");
      return;
    }
    if (password !== passwordConfirm) {
      setError("Les mots de passe ne correspondent pas.");
      return;
    }

    // Ici tu peux ajouter la logique d'API ou mock stockage

    // Mock succès
    setSuccess(true);

    // Après 2s, redirige vers login
    setTimeout(() => {
      router.push("/login");
    }, 2000);
  };

  return (
    <main className="min-h-screen bg-gradient-to-r from-purple-600 to-pink-600 flex flex-col items-center justify-center text-white px-6">
      <h1 className="text-4xl font-extrabold mb-8 drop-shadow-lg">Inscription</h1>

      <form
        onSubmit={handleSignup}
        className="bg-white bg-opacity-10 backdrop-blur-md rounded p-8 w-full max-w-md"
      >
        {error && (
          <p className="mb-4 text-red-400 font-semibold">{error}</p>
        )}
        {success && (
          <p className="mb-4 text-green-400 font-semibold">
            Inscription réussie ! Redirection vers la connexion...
          </p>
        )}

        <label className="block mb-2 font-semibold text-white">Email</label>
        <input
          type="email"
          className="w-full mb-4 px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          disabled={success}
          required
        />

        <label className="block mb-2 font-semibold text-white">Mot de passe</label>
        <input
          type="password"
          className="w-full mb-4 px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          disabled={success}
          required
        />

        <label className="block mb-2 font-semibold text-white">Confirmer mot de passe</label>
        <input
          type="password"
          className="w-full mb-6 px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400"
          value={passwordConfirm}
          onChange={(e) => setPasswordConfirm(e.target.value)}
          disabled={success}
          required
        />

        <button
          type="submit"
          disabled={success}
          className="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded transition disabled:opacity-50"
        >
          S’inscrire
        </button>
      </form>
    </main>
  );
}
