// GadgetGalaxy Ecommerce Website

import React, { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { GoogleAuthProvider, getAuth, signInWithPopup } from "firebase/auth";
import { initializeApp } from "firebase/app";
import { Card, CardContent } from "@/components/ui/card";

const firebaseConfig = {
  apiKey: "<?php echo $firebaseKey; ?>",
  authDomain: "rizzauthapp.firebaseapp.com",
  projectId: "rizzauthapp",
  storageBucket: "rizzauthapp.appspot.com",
  messagingSenderId: "607508317395",
  appId: "1:607508317395:web:f2f403d10915d6d2ef4026",
  measurementId: "G-2YQFBWK95F"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

export default function Home() {
  const [products, setProducts] = useState([]);
  const [user, setUser] = useState(null);

  const fetchProducts = async () => {
    const res = await fetch("http://localhost/Roshan/api/products.php");
    const data = await res.json();
    setProducts(data);
  };

  const handleGoogleSignIn = async () => {
    try {
      const result = await signInWithPopup(auth, provider);
      const user = result.user;
      setUser(user);
      await fetch("http://localhost/Roshan/save-user.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          uid: user.uid,
          name: user.displayName,
          email: user.email,
        }),
      });
    } catch (error) {
      console.error("Login Failed:", error);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  return (
    <div className="min-h-screen bg-white text-black">
      <header className="p-4 bg-blue-900 text-white flex justify-between items-center">
        <h1 className="text-3xl font-bold">GadgetGalaxy</h1>
        <div>
          {user ? (
            <span>Welcome, {user.displayName}</span>
          ) : (
            <button
              onClick={handleGoogleSignIn}
              className="bg-white text-blue-900 px-4 py-2 rounded font-semibold"
            >
              <span style="font-family: 'Product Sans', sans-serif;">
              <span style="color: #4285F4;">G</span>
              <span style="color: #EA4335;">o</span>
              <span style="color: #FBBC05;">o</span>
              <span style="color: #4285F4;">g</span>
              <span style="color: #34A853;">l</span>
              <span style="color: #EA4335;">e</span>
              </span>
            </button>
          )}
        </div>
      </header>

      <main className="p-6">
        <motion.h2
          className="text-2xl font-semibold mb-4"
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          Explore our latest tech deals 💻📱⌚
        </motion.h2>

        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.map((product, i) => (
            <motion.div
              key={i}
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: i * 0.1 }}
            >
              <Card className="rounded-2xl overflow-hidden shadow-lg hover:scale-105 transition">
                <img src={product.image} alt={product.name} className="w-full h-40 object-cover" />
                <CardContent className="p-4">
                  <h3 className="text-lg font-bold">{product.name}</h3>
                  <p className="text-sm text-gray-600">₹{product.price}</p>
                  <p className="text-yellow-500">⭐ {product.rating}</p>
                  <button className="mt-2 px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add to Cart
                  </button>
                </CardContent>
              </Card>
            </motion.div>
          ))}
        </div>
      </main>

      <footer className="bg-blue-900 text-white p-4 text-center mt-10">
        &copy; 2025 GadgetGalaxy. All rights reserved.
      </footer>
    </div>
  );
}
