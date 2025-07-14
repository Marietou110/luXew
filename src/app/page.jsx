"use client";
import Link from "next/link";
import { useEffect, useState } from "react";

const featuredPoints = [
  { icon: "📰", title: "Articles récents", desc: "Accédez aux dernières actualités rapidement." },
  { icon: "⚙️", title: "Gestion simplifiée", desc: "Ajoutez, modifiez et supprimez vos contenus facilement." },
  { icon: "👥", title: "Multi-profils", desc: "Visiteurs, éditeurs et administrateurs avec rôles distincts." },
];

const mockArticles = [
  { id: 1, title: "Article 1", summary: "Résumé rapide de l'article 1" },
  { id: 2, title: "Article 2", summary: "Résumé rapide de l'article 2" },
  { id: 3, title: "Article 3", summary: "Résumé rapide de l'article 3" },
  { id: 4, title: "Article 4", summary: "Résumé rapide de l'article 4" },
  { id: 5, title: "Article 5", summary: "Résumé rapide de l'article 5" },
];

export default function Home() {
  const [userRole, setUserRole] = useState(null);
  const [page, setPage] = useState(1);
  const articlesPerPage = 3;

  useEffect(() => {
    setUserRole(localStorage.getItem("userRole"));
  }, []);

  const displayedArticles = mockArticles.slice(
    (page - 1) * articlesPerPage,
    page * articlesPerPage
  );

  const totalPages = Math.ceil(mockArticles.length / articlesPerPage);

  return (
    <div className="flex flex-col min-h-screen">
      {/* Header */}
      <header className="bg-indigo-900 text-white flex justify-between items-center px-8 py-4 fixed top-0 w-full z-50 shadow">
        <div className="text-2xl font-bold cursor-pointer">
          <Link href="/">MonSiteActualités</Link>
        </div>
        <nav className="space-x-6 text-lg">
          <Link href="/" className="hover:text-indigo-300">Accueil</Link>
          <Link href="/articles" className="hover:text-indigo-300">Articles</Link>
          <Link href="/categories" className="hover:text-indigo-300">Catégories</Link>
          {!userRole ? (
            <Link href="/login" className="hover:text-indigo-300">Connexion</Link>
          ) : (
            <Link href="/dashboard" className="hover:text-indigo-300">Dashboard</Link>
          )}
        </nav>
      </header>

      {/* Hero */}
      <section className="pt-24 bg-gradient-to-r from-indigo-700 to-indigo-900 text-white text-center px-6 py-20">
        <h1 className="text-5xl font-extrabold mb-6 drop-shadow-lg">
          Bienvenue sur MonSiteActualités
        </h1>
        <p className="max-w-xl mx-auto text-lg mb-8 drop-shadow">
          Découvrez les dernières actualités et gérez vos contenus en toute simplicité.
        </p>
        <div className="space-x-4">
          <Link
            href="/articles"
            className="bg-white text-indigo-900 font-bold px-8 py-3 rounded shadow hover:bg-gray-200 transition"
          >
            Voir les articles
          </Link>
          {!userRole && (
            <Link
              href="/login"
              className="border border-white px-8 py-3 rounded hover:bg-white hover:text-indigo-900 transition"
            >
              Se connecter
            </Link>
          )}
        </div>
      </section>

      {/* Features */}
      <section className="bg-gray-100 py-16 px-6 text-center">
        <div className="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
          {featuredPoints.map(({ icon, title, desc }) => (
            <div key={title} className="bg-white p-8 rounded shadow hover:shadow-lg transition">
              <div className="text-5xl mb-4">{icon}</div>
              <h3 className="text-xl font-semibold mb-2">{title}</h3>
              <p className="text-gray-700">{desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Derniers articles */}
      <section className="flex-grow max-w-5xl mx-auto px-6 py-12">
        <h2 className="text-3xl font-bold mb-8 text-center">Derniers articles</h2>
        <ul className="space-y-6">
          {displayedArticles.map(({ id, title, summary }) => (
            <li key={id} className="bg-white p-6 rounded shadow hover:shadow-md transition cursor-pointer">
              <Link href={`/articles/${id}`}>
                <h3 className="text-2xl font-semibold mb-2 text-indigo-900 hover:underline">{title}</h3>
                <p className="text-gray-700">{summary}</p>
              </Link>
            </li>
          ))}
        </ul>

        {/* Pagination */}
        <div className="flex justify-center space-x-4 mt-8">
          <button
            onClick={() => setPage((p) => Math.max(p - 1, 1))}
            disabled={page === 1}
            className="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-50 hover:bg-indigo-700 transition"
          >
            Précédent
          </button>
          <span className="px-4 py-2 text-indigo-900 font-semibold">{page} / {totalPages}</span>
          <button
            onClick={() => setPage((p) => Math.min(p + 1, totalPages))}
            disabled={page === totalPages}
            className="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-50 hover:bg-indigo-700 transition"
          >
            Suivant
          </button>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-indigo-900 text-white text-center py-6 mt-auto">
        © 2025 MonSiteActualités - Tous droits réservés
      </footer>
    </div>
  );
}
