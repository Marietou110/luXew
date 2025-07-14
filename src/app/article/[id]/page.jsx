"use client";
import { useParams, useRouter } from "next/navigation";

const articlesData = [
  { id: 1, title: "Article 1", summary: "Résumé complet de l'article 1..." },
  { id: 2, title: "Article 2", summary: "Résumé complet de l'article 2..." },
  { id: 3, title: "Article 3", summary: "Résumé complet de l'article 3..." },
  { id: 4, title: "Article 4", summary: "Résumé complet de l'article 4..." },
];

export default function ArticleDetails() {
  const params = useParams();
  const router = useRouter();
  const articleId = parseInt(params.id);

  const article = articlesData.find((a) => a.id === articleId);

  if (!article) {
    return (
      <div className="container mx-auto p-8">
        <h1 className="text-2xl font-bold text-red-600">
          Article introuvable
        </h1>
        <button
          onClick={() => router.back()}
          className="mt-4 px-4 py-2 bg-blue-500 text-white rounded"
        >
          Retour
        </button>
      </div>
    );
  }

  return (
    <div className="container mx-auto p-8">
      <h1 className="text-4xl font-bold text-blue-800 mb-6">{article.title}</h1>
      <p className="text-lg text-gray-700">{article.summary}</p>

      <button
        onClick={() => router.back()}
        className="mt-8 px-4 py-2 bg-blue-500 text-white rounded"
      >
        Retour
      </button>
    </div>
  );
}
