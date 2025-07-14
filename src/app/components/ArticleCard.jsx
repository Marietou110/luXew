import React from "react";

const ArticleCard = ({ article, onClick }) => {
  return (
    <div
      className="bg-white rounded-xl shadow p-4 cursor-pointer hover:scale-105 transition-transform"
      onClick={() => onClick(article.id)}
    >
      <h2 className="text-2xl font-bold text-blue-700 mb-2">{article.title}</h2>
      <p className="text-gray-600">{article.summary}</p>
    </div>
  );
};

export default ArticleCard;
