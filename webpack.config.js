const path = require('path');

module.exports = {
  entry: './assets/js/userwall-wp.js', // Your main JavaScript entry file
  output: {
    filename: 'userwall-wp.js', // Output filename
    path: path.resolve(__dirname, 'assets/js'), // Output directory
  },
  module: {
    rules: [
      {
        test: /\.js$/, // Apply this rule to .js files
        exclude: /node_modules/, // Exclude node_modules directory
        use: {
          loader: 'babel-loader', // Use babel-loader for JavaScript files
          options: {
            presets: ['@babel/preset-env'], // Babel preset for modern JavaScript
          },
        },
      },
    ],
  },
};
