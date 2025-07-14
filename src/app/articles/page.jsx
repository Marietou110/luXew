"use client";
import { useState } from "react";

const initialArticles = [
  { id: 1, title: "Premier article", content: "Contenu du premier article." },
  { id: 2, title: "Deuxième article", content: "Contenu du deuxième article." },
];

export default function Articles() {
  const [articles, setArticles] = useState(initialArticles);
  const [title, setTitle] = useState("");
  const [content, setContent] = useState("");

  // Ajouter un article
  const handleAdd = () => {
    if (title.trim() === "" || content.trim() === "") {
      alert("Merci de remplir titre et contenu !");
      return;
    }
    const newArticle = {
      id: articles.length ? articles[articles.length - 1].id + 1 : 1,
      title,
      content,
    };
    setArticles([...articles, newArticle]);
    setTitle("");
    setContent("");
  };

  // Supprimer un article
  const handleDelete = (id) => {
    setArticles(articles.filter((a) => a.id !== id));
  };

  return (
    <div className="container mx-auto p-8">
      <h1 className="text-3xl font-bold mb-6">Gestion des Articles</h1>

      <div className="mb-6">
        <input
          type="text"
          placeholder="Titre"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          className="border px-3 py-2 mr-3 rounded w-1/3"
        />
        <input
          type="text"
          placeholder="Contenu"
          value={content}
          onChange={(e) => setContent(e.target.value)}
          className="border px-3 py-2 mr-3 rounded w-1/2"
        />
        <button
          onClick={handleAdd}
          className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Ajouter
        </button>
      </div>

      <ul>
        {articles.map((article) => (
          <li
            key={article.id}
            className="mb-4 p-4 border rounded flex justify-between items-center"
          >
            <div>
              <h2 className="font-bold text-lg">{article.title}</h2>
              <p>{article.content}</p>
            </div>
            <button
              onClick={() => handleDelete(article.id)}
              className="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
            >
              Supprimer
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}
