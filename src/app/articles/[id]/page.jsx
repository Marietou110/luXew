"use client";
import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import Link from "next/link";

const mockArticles = [
  { id: 1, title: "Article 1", content: "Contenu détaillé de l’article 1...", categoryId: 1 },
  { id: 2, title: "Article 2", content: "Contenu détaillé de l’article 2...", categoryId: 2 },
  { id: 3, title: "Article 3", content: "Contenu détaillé de l’article 3...", categoryId: 1 },
];

const mockCategories = [
  { id: 1, name: "Politique" },
  { id: 2, name: "Sport" },
];

export default function ArticleDetail() {
  const { id } = useParams();
  const articleId = Number(id);
  const [article, setArticle] = useState(null);
  const [categoryName, setCategoryName] = useState("");
  const router = useRouter();

  useEffect(() => {
    const foundArticle = mockArticles.find((a) => a.id === articleId);
    if (!foundArticle) {
      // Article non trouvé → retour à la liste
      router.push("/articles");
      return;
    }
    setArticle(foundArticle);

    const cat = mockCategories.find((c) => c.id === foundArticle.categoryId);
    setCategoryName(cat ? cat.name : "Catégorie inconnue");
  }, [articleId, router]);

  if (!article) return <p>Chargement...</p>;

  return (
    <div className="max-w-4xl mx-auto p-8">
      <h1 className="text-4xl font-bold mb-4 text-indigo-900">{article.title}</h1>
      <p className="mb-2 text-sm text-gray-500">Catégorie : {categoryName}</p>
      <article className="prose prose-indigo mb-8">{article.content}</article>

      <Link
        href={`/categories/${article.categoryId}`}
        className="text-indigo-600 hover:underline font-semibold"
      >
        ← Retour à la catégorie {categoryName}
      </Link>
    </div>
  );
}
