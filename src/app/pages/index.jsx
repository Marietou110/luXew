import React, { useState } from "react";
import ArticleCard from "../components/ArticleCard";
import Pagination from "../components/Pagination";

// Mock data temporaire (à remplacer par API plus tard)
const articlesData = [
  { id: 1, title: "Article 1", summary: "Résumé de l'article 1..." },
  { id: 2, title: "Article 2", summary: "Résumé de l'article 2..." },
  { id: 3, title: "Article 3", summary: "Résumé de l'article 3..." },
  { id: 4, title: "Article 4", summary: "Résumé de l'article 4..." },
  { id: 5, title: "Article 5", summary: "Résumé de l'article 5..." },
  { id: 6, title: "Article 6", summary: "Résumé de l'article 6..." },
];

const ARTICLES_PER_PAGE = 2;

const HomePage = () => {
  const [currentPage, setCurrentPage] = useState(1);

  const totalPages = Math.ceil(articlesData.length / ARTICLES_PER_PAGE);

  const handlePageChange = (page) => {
    if (page >= 1 && page <= totalPages) {
      setCurrentPage(page);
    }
  };

  const handleArticleClick = (id) => {
    console.log("Clicked article ID:", id);
    // TODO: Naviguer vers la page de détails
  };

  const startIndex = (currentPage - 1) * ARTICLES_PER_PAGE;
  const currentArticles = articlesData.slice(
    startIndex,
    startIndex + ARTICLES_PER_PAGE
  );

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold text-center text-blue-800 mb-8">
        Derniers Articles
      </h1>
      <div className="grid grid-cols-1 gap-6">
        {currentArticles.map((article) => (
          <ArticleCard
            key={article.id}
            article={article}
            onClick={handleArticleClick}
          />
        ))}
      </div>
      <Pagination
        currentPage={currentPage}
        totalPages={totalPages}
        onPageChange={handlePageChange}
      />
    </div>
  );
};

export default HomePage;
