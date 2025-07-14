"use client";
import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import Link from "next/link";

const mockArticles = [
  { id: 1, title: "Article 1", summary: "Résumé article 1", categoryId: 1 },
  { id: 2, title: "Article 2", summary: "Résumé article 2", categoryId: 2 },
  { id: 3, title: "Article 3", summary: "Résumé article 3", categoryId: 1 },
  { id: 4, title: "Article 4", summary: "Résumé article 4", categoryId: 3 },
  { id: 5, title: "Article 5", summary: "Résumé article 5", categoryId: 2 },
];

const mockCategories = [
  { id: 1, name: "Politique" },
  { id: 2, name: "Sport" },
  { id: 3, name: "Technologie" },
];

export default function CategoryArticles() {
  const params = useParams();
  const categoryId = Number(params.id);
  const [articles, setArticles] = useState([]);
  const [categoryName, setCategoryName] = useState("");

  useEffect(() => {
    // Trouver le nom de la catégorie
    const category = mockCategories.find((cat) => cat.id === categoryId);
    setCategoryName(category ? category.name : "Catégorie inconnue");

    // Filtrer les articles
    const filteredArticles = mockArticles.filter(
      (article) => article.categoryId === categoryId
    );
    setArticles(filteredArticles);
  }, [categoryId]);

  return (
    <div className="max-w-5xl mx-auto p-8">
      <h1 className="text-4xl font-bold mb-6 text-indigo-900">
        Articles dans la catégorie : {categoryName}
      </h1>

      {articles.length === 0 ? (
        <p>Aucun article trouvé dans cette catégorie.</p>
      ) : (
        <ul className="space-y-6">
          {articles.map(({ id, title, summary }) => (
            <li
              key={id}
              className="bg-indigo-50 p-6 rounded shadow hover:shadow-md transition cursor-pointer"
            >
              <Link href={`/articles/${id}`}>
                <h3 className="text-2xl font-semibold mb-2 text-indigo-900 hover:underline">
                  {title}
                </h3>
                <p className="text-gray-700">{summary}</p>
              </Link>
            </li>
          ))}
        </ul>
      )}

      <div className="mt-8">
        <Link
          href="/categories"
          className="text-indigo-600 hover:underline font-semibold"
        >
          ← Retour aux catégories
        </Link>
      </div>
    </div>
  );
}
