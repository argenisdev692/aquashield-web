import { useState } from 'react';

interface CounterProps {
  initialCount?: number;
}

export default function Counter({ initialCount = 0 }: CounterProps) {
  const [count, setCount] = useState(initialCount);

  return (
    <div className="counter">
      <h2>React Counter Component</h2>
      <p>Count: {count}</p>
      <button onClick={() => setCount(count + 1)}>Increment</button>
      <button onClick={() => setCount(count - 1)}>Decrement</button>
      <button onClick={() => setCount(0)}>Reset</button>
      <style>{`
        .counter {
          padding: 1rem;
          border: 2px solid #4f46e5;
          border-radius: 8px;
          text-align: center;
          margin: 1rem 0;
        }
        .counter button {
          margin: 0.25rem;
          padding: 0.5rem 1rem;
          background: #4f46e5;
          color: white;
          border: none;
          border-radius: 4px;
          cursor: pointer;
        }
        .counter button:hover {
          background: #4338ca;
        }
      `}</style>
    </div>
  );
}
