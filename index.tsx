import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import App from "./App";
import PurchasePage from "./PurchasePage";
import PurchaseSuccess from "./PurchaseSuccess";
import "./index.css";

const rootElement = document.getElementById("root");
if (!rootElement) {
  throw new Error("Could not find root element to mount to");
}

const root = ReactDOM.createRoot(rootElement);
root.render(
  <React.StrictMode>
    <BrowserRouter>
      <Routes>
        {/* Portfolio home (existing App) */}
        <Route path="/" element={<App />} />

        {/* Licensing purchase flow */}
        <Route path="/purchase/:slug" element={<PurchasePage />} />
        <Route
          path="/purchase/:slug/success"
          element={<PurchaseSuccess />}
        />
      </Routes>
    </BrowserRouter>
  </React.StrictMode>
);
