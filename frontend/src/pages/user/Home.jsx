import React, { useState, useEffect } from "react";
import api from "../../api/api.js";

export default function Home() {
  const [stories, setStories] = useState([]);
  const [currentPage, setCurrentPage] = useState(1); // current page
  const [storiesPerPage] = useState(10); // stories per page

  useEffect(() => {
    api
      .get("/stories")
      .then((res) => {
        setStories(res.data.result);
      })
      .catch((err) => console.error(err));
  }, []);

  // Calculate indices for pagination
  const indexOfLastStory = currentPage * storiesPerPage;
  const indexOfFirstStory = indexOfLastStory - storiesPerPage;
  const currentStories = stories.slice(indexOfFirstStory, indexOfLastStory);

  // Page numbers
  const pageNumbers = [];
  for (let i = 1; i <= Math.ceil(stories.length / storiesPerPage); i++) {
    pageNumbers.push(i);
  }

  // Change page
  const paginate = (pageNumber) => setCurrentPage(pageNumber);

  return (
    <div style={{ width: "80%", margin: "auto", marginTop: "5%" }}>
      <table className="table table-hover">
        <thead className="thead-dark">
          <tr>
            <th>No</th>
            <th>Title</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          {currentStories.map((story, index) => (
            <tr key={story.id}>
              <td>{indexOfFirstStory + index + 1}</td>
              <td>{story.title}</td>
              <td>{story.body}</td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Pagination */}
      <nav>
        <ul className="pagination justify-content-center">
          {pageNumbers.map((number) => (
            <li
              key={number}
              className={`page-item ${number === currentPage ? "active" : ""}`}
            >
              <button onClick={() => paginate(number)} className="page-link">
                {number}
              </button>
            </li>
          ))}
        </ul>
      </nav>
    </div>
  );
}
